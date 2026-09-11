#!/usr/bin/env python3
"""Gera os dois zips instalaveis a partir de um unico codigo-fonte.

Sao duas instalacoes separadas do WordPress, entao cada uma recebe o seu zip.
Mas o codigo e um so: corrigir um bug aqui sai nas duas builds. Manter dois
temas copiados a mao seria garantir que eles divergem em poucos meses.

Sao dois sites independentes, em dois WordPress e dois dominios. Nenhum deles
e bilingue: cada zip ja vem no seu idioma, cravado no codigo. Instalou, esta
no idioma certo - sem depender de Configuracoes > Idioma do site, sem plugin
de traducao, sem seletor de idioma.

O que muda entre as builds:
  - o nome da pasta e do tema (aparece em Aparencia > Temas)
  - os textos da interface, substituidos a partir do .po

Uso:
    python3 tools/build-themes.py
Saida em dist/.
"""

import pathlib
import re
import shutil
import zipfile

ROOT = pathlib.Path(__file__).resolve().parent.parent
SRC = ROOT / 'themes' / 'quality-blog'
DIST = ROOT / 'dist'

BUILDS = [
    {
        'slug': 'quality-blog',
        'name': 'Quality Blog',
        'description': 'Tema do Quality Blog - versao em ingles. Gerado por tools/build-themes.py.',
        'po': None,            # ja esta em ingles no fonte
    },
    {
        'slug': 'blog-de-la-calidad',
        'name': 'Blog de la Calidad',
        'description': 'Tema del Blog de la Calidad - version en espanol. Generado por tools/build-themes.py.',
        'po': 'es_ES.po',      # textos trocados no build
    },
]

EXCLUDE = {'.DS_Store'}

# Chamadas de traducao cujo primeiro argumento e o texto a trocar.
GETTEXT = r"(__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e|_x|esc_html_x|esc_attr_x)"


def load_po(path):
    """Le o .po e devolve {texto original: traducao}. Ignora plurais."""
    out, cur, key = {}, {}, None
    for raw in path.read_text(encoding='utf-8').split('\n'):
        line = raw.strip()
        if not line or line.startswith('#'):
            if cur.get('msgid') and cur.get('msgstr'):
                out[cur['msgid']] = cur['msgstr']
            cur, key = {}, None
            continue
        m = re.match(r'(msgctxt|msgid_plural|msgid|msgstr)\s+"(.*)"$', line)
        if m:
            key = m.group(1)
            cur[key] = unescape(m.group(2))
        elif line.startswith('"') and key:
            cur[key] += unescape(line[1:-1])
    if cur.get('msgid') and cur.get('msgstr'):
        out[cur['msgid']] = cur['msgstr']
    out.pop('', None)
    return out


def unescape(s):
    out, i = [], 0
    while i < len(s):
        if s[i] == '\\' and i + 1 < len(s):
            out.append({'n': '\n', 't': '\t', '"': '"', '\\': '\\'}.get(s[i + 1], s[i + 1]))
            i += 2
        else:
            out.append(s[i]); i += 1
    return ''.join(out)


def substitute(text, table):
    """Troca o texto dentro de __( '...', 'quality-blog' ) e afins.

    Mexe so no primeiro argumento e preserva aspas, escapes e os %1$s do
    sprintf - por isso a troca e feita na chamada, nao no arquivo inteiro.
    """
    def repl(m):
        fn, quote, body = m.group(1), m.group(2), m.group(3)
        original = unescape(body)
        if original not in table:
            return m.group(0)
        new = table[original].replace('\\', '\\\\').replace(quote, '\\' + quote)
        return f"{fn}( {quote}{new}{quote}" + m.group(4)

    pattern = GETTEXT + r"\(\s*(['\"])((?:\\.|(?!\2).)*)\2(\s*,)"
    return re.sub(pattern, repl, text)


def count_changes(a, b):
    return sum(1 for x, y in zip(a.split('\n'), b.split('\n')) if x != y)



def build(conf):
    out_dir = DIST / conf['slug']
    if out_dir.exists():
        shutil.rmtree(out_dir)

    shutil.copytree(
        SRC, out_dir,
        ignore=shutil.ignore_patterns(*EXCLUDE),
    )

    # style.css: nome e descricao do tema
    style = out_dir / 'style.css'
    css = style.read_text(encoding='utf-8')
    css = re.sub(r'^Theme Name: .*$', f"Theme Name: {conf['name']}", css, count=1, flags=re.M)
    css = re.sub(r'^Description: .*$', f"Description: {conf['description']}", css, count=1, flags=re.M)
    style.write_text(css, encoding='utf-8')

    # textos da interface: troca os literais dentro das chamadas de traducao
    replaced = 0
    if conf['po']:
        table = load_po(SRC / 'languages' / conf['po'])
        for php_file in out_dir.rglob('*.php'):
            text = php_file.read_text(encoding='utf-8')
            new = substitute(text, table)
            if new != text:
                replaced += count_changes(text, new)
                php_file.write_text(new, encoding='utf-8')

    # languages/ so faz sentido no fonte; no zip os textos ja estao trocados
    langs = out_dir / 'languages'
    if langs.exists():
        shutil.rmtree(langs)

    # zip com a pasta do tema na raiz, que e o que o WordPress espera
    zip_path = DIST / f"{conf['slug']}.zip"
    if zip_path.exists():
        zip_path.unlink()
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as z:
        for f in sorted(out_dir.rglob('*')):
            if f.is_file() and f.name not in EXCLUDE:
                z.write(f, pathlib.Path(conf['slug']) / f.relative_to(out_dir))

    files = sum(1 for f in out_dir.rglob('*') if f.is_file())
    kb = zip_path.stat().st_size // 1024
    lang = f"{replaced} textos traduzidos" if conf['po'] else "textos do fonte (ingles)"
    print(f"  {conf['name']:<22} {conf['slug']+'.zip':<26} {files:>3} arquivos  {kb:>4}KB  {lang}")
    return zip_path


if __name__ == '__main__':
    DIST.mkdir(exist_ok=True)
    print(f"origem: {SRC.relative_to(ROOT)}\n")
    for conf in BUILDS:
        build(conf)
    print(f"\nzips em {DIST.relative_to(ROOT)}/")
