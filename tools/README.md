# tools/

Scripts de uma vez só, para preparar uma instalação do WordPress com o tema.
Não fazem parte do tema — não vão dentro do zip.

## Por que existem

Tema é código e viaja no zip. **Menu, categorias e configurações são conteúdo
e ficam no banco** — não viajam. Instalar o tema num WordPress novo dá um
cabeçalho e um rodapé vazios até alguém montar os menus. Estes scripts fazem
isso em segundos, igual em toda instalação.

## No Local (máquina de teste)

Com o site **rodando** no Local:

```bash
./tools/local.sh setup-site
./tools/local.sh setup-menu
```

Para o site em espanhol:

```bash
./tools/local.sh setup-menu es
```

O `local.sh` descobre sozinho o PHP do Local, a pasta do site e o socket do
MySQL. Se você tiver mais de um site no Local, passe o nome:

```bash
./tools/local.sh setup-site nome-do-site
```

## Num servidor de verdade

Precisa de acesso a terminal na máquina. Se a TI não der, dá para fazer tudo
pelo painel — é mais demorado, mas são as mesmas ações.

```bash
WP_PUBLIC=/caminho/do/wordpress php tools/setup-site.php
WP_PUBLIC=/caminho/do/wordpress php tools/setup-menu.php
LANG=es WP_PUBLIC=/caminho/do/wordpress php tools/setup-menu.php
```

## O que cada um faz

| Script | O que faz |
|---|---|
| `setup-site.php` | Ativa o tema, links permanentes `/%postname%/`, home nos posts recentes, moderação manual de comentários |
| `setup-menu.php` | Menu principal (Software, Categories, Free Assets, Quality Assistant) e as três colunas do rodapé; atribui a todos os locais do tema |
| `build-themes.py` | Gera os dois zips em `dist/`, a partir de `themes/quality-blog/` |
| `extract_strings.py` | Atualiza `languages/quality-blog.pot` com as strings traduzíveis |
| `local.sh` | Atalho para rodar os dois primeiros num site do Local |

**Atenção:** `setup-menu.php` apaga e recria os menus. Edições feitas à mão no
painel se perdem se você rodar de novo.

## Ordem numa instalação nova

1. Instalar e ativar o tema (`Aparência → Temas → Adicionar → Enviar tema`)
2. `setup-site.php`
3. Criar as categorias, ou importar o conteúdo
4. `setup-menu.php` — **depois** das categorias, senão o dropdown nasce vazio
5. Personalizar → Rodapé e redes: endereços das redes e destino do Subscribe
6. Aparência → Menus: trocar os `#` pelas URLs reais

## Conteúdo de demonstração

`themes/quality-blog-demo-content.xml` traz 27 posts, 9 categorias e 7 autores
do protótipo, para uma instalação de teste não ficar vazia. Importe por
**Ferramentas → Importar → WordPress**.

O corpo dos posts é texto de preenchimento, com aviso no primeiro parágrafo.
**Não use em produção.**
