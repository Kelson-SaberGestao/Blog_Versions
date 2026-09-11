# tools/

Scripts de uma vez só, para preparar uma instalação nova do WordPress com o
tema Quality Blog. Não fazem parte do tema — não vão no zip.

## Por que existem

Tema é código e viaja no zip. **Menu, categorias e configurações são conteúdo
e ficam no banco** — não viajam. Instalar o tema num servidor novo dá um
cabeçalho vazio até alguém montar o menu. Estes scripts fazem isso em segundos,
igual em toda instalação, sem depender de ninguém lembrar da sequência de cliques.

## Como rodar

Precisa do PHP. Se não tiver no sistema, use o que vem com o Local:

```bash
PHP="$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
```

Depois, apontando para a pasta do WordPress:

```bash
WP_PUBLIC="$HOME/Local Sites/quality-blog/app/public" "$PHP" tools/setup-site.php
WP_PUBLIC="$HOME/Local Sites/quality-blog/app/public" "$PHP" tools/setup-menu.php
```

**No Local**, o MySQL escuta num socket e a conexão por linha de comando falha
com *"Error establishing a database connection"*. Descubra o socket e passe junto:

```bash
find ~/Library/Application\ Support/Local/run -name "mysqld.sock"
```

```bash
WP_PUBLIC="..." DB_SOCKET="/caminho/para/mysqld.sock" "$PHP" tools/setup-site.php
```

Num servidor de verdade (produção ou staging), o `DB_SOCKET` não é necessário.

## O que cada um faz

| Script | O que faz |
|---|---|
| `setup-site.php` | Ativa o tema, define links permanentes como `/%postname%/`, aponta a home para os posts recentes, liga moderação manual de comentários |
| `setup-menu.php` | Cria o menu principal com Software for Quality (12), Categories (as categorias reais), Free Assets (3) e Quality Assistant; atribui a `primary` e `mobile` |

Os dois são idempotentes — rodar de novo não duplica nada. O `setup-menu.php`
apaga e recria o menu, então **edições feitas à mão no painel se perdem** se
você rodar de novo.

## Conteúdo de demonstração

O arquivo `themes/quality-blog-demo-content.xml` traz 27 posts, 9 categorias e
7 autores do protótipo, para uma instalação nova não ficar vazia. Importe por
**Ferramentas → Importar → WordPress**.

O corpo dos posts é texto de preenchimento, com aviso no primeiro parágrafo.
**Não use em produção** — é só para ver o tema com conteúdo.

## Ordem numa instalação nova

1. Instalar e ativar o tema (`Aparência → Temas → Adicionar → Enviar tema`)
2. `setup-site.php`
3. Importar o conteúdo (real, ou o demo se for teste)
4. `setup-menu.php` — depois das categorias existirem, senão o dropdown sai vazio
5. Ajustar no painel: menus do rodapé, redes sociais, destino do Subscribe
