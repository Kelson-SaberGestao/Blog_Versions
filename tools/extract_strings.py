#!/usr/bin/env python3
"""Extrai as strings traduziveis do tema e gera o .pot.

Substitui o xgettext, que nao esta instalado nesta maquina.
Cobre: __, _e, _x, _n, esc_html__, esc_html_e, esc_html_x, esc_attr__,
esc_attr_e, esc_attr_x.
"""
import re, sys, pathlib, collections

ROOT = pathlib.Path(sys.argv[1] if len(sys.argv) > 1 else 'themes/quality-blog')
DOMAIN = 'quality-blog'

# funcao( 'texto' [, 'contexto'] , 'dominio' )
SIMPLE = r"""\b(?:esc_html__|esc_html_e|esc_attr__|esc_attr_e|__|_e)\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*['"]%s['"]\s*\)""" % DOMAIN
CTX    = r"""\b(?:esc_html_x|esc_attr_x|_x)\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*(['"])((?:\\.|(?!\3).)*)\3\s*,\s*['"]%s['"]\s*\)""" % DOMAIN
PLURAL = r"""\b_n\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*(['"])((?:\\.|(?!\3).)*)\3\s*,""" 

entries = collections.OrderedDict()   # (ctx, singular, plural) -> [refs]

def add(key, ref):
    entries.setdefault(key, []).append(ref)

for path in sorted(ROOT.rglob('*.php')):
    src = path.read_text(encoding='utf-8')
    rel = path.relative_to(ROOT.parent.parent) if ROOT.parent.parent in path.parents else path
    for m in re.finditer(SIMPLE, src):
        line = src[:m.start()].count('\n') + 1
        add((None, m.group(2), None), f"{rel}:{line}")
    for m in re.finditer(CTX, src):
        line = src[:m.start()].count('\n') + 1
        add((m.group(4), m.group(2), None), f"{rel}:{line}")
    for m in re.finditer(PLURAL, src):
        line = src[:m.start()].count('\n') + 1
        add((None, m.group(2), m.group(4)), f"{rel}:{line}")

def esc(s):
    return s.replace('\\', '\\\\').replace('"', '\\"')

out = ['''# Quality Blog - modelo de traducao.
# Gerado por tools/extract_strings.py
msgid ""
msgstr ""
"Project-Id-Version: Quality Blog\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
''']

for (ctx, sing, plur), refs in entries.items():
    out.append('')
    out.append('#: ' + ' '.join(refs))
    if ctx:
        out.append('msgctxt "%s"' % esc(ctx))
    out.append('msgid "%s"' % esc(sing))
    if plur:
        out.append('msgid_plural "%s"' % esc(plur))
        out.append('msgstr[0] ""')
        out.append('msgstr[1] ""')
    else:
        out.append('msgstr ""')

dest = ROOT / 'languages' / f'{DOMAIN}.pot'
dest.write_text('\n'.join(out) + '\n', encoding='utf-8')
print(f"{len(entries)} strings -> {dest}")
