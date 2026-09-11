# Quality Blog — tema WordPress

Tema do Quality Blog (versão em inglês do Blog da Qualidade), convertido do protótipo
estático `index.html` / `post.html`.

## Instalação

1. No repositório, gere o zip: `cd themes && zip -r quality-blog.zip quality-blog`
2. No WordPress: **Aparência → Temas → Adicionar novo → Enviar tema**
3. Envie o `quality-blog.zip` e clique em **Ativar**

Não é preciso acesso a servidor, FTP ou banco — só a conta de administrador.

## Configuração depois de ativar

Nesta ordem, porque os dois primeiros itens são caros de mudar depois:

**1. Links permanentes** — Configurações → Links permanentes → **Nome do post**.
Defina antes de publicar. Mudar depois quebra URL e exige redirect 301.

**2. Categorias** — crie as 9 categorias com estes slugs exatos, senão a cor
do bloco "Browse by topic" cai no azul padrão:

| Categoria | Slug | Cor |
|---|---|---|
| Quality Management Systems | `quality-management-systems` | `#0544AB` |
| Quality Tools | `quality-tools` | `#0E8F8F` |
| Continuous Improvement | `continuous-improvement` | `#16A34A` |
| Organizational Culture | `organizational-culture` | `#C026A3` |
| Project Management | `project-management` | `#7C3AED` |
| Business Strategy | `business-strategy` | `#D4841A` |
| Quality Gurus | `quality-gurus` | `#1E3A8A` |
| Process Management | `process-management` | `#0369A1` |
| Philosophy of Excellence | `philosophy-of-excellence` | `#B91C1C` |

Para mudar uma cor, edite `qb_topic_colors()` em `functions.php` — não o CSS.

**3. Home** — Configurações → Leitura → "Sua página inicial exibe: **Seus posts mais
recentes**". O `front-page.php` assume isso.

**4. Menu** — Aparência → Menus → criar e marcar como **Menu principal**.
Item com subitens vira dropdown. Para o destaque azul do Quality Assistant,
abra as Opções de tela do editor de menus, ligue **Classes CSS**, e escreva
`qa-link` no item.

**5. Banner do topo** — Aparência → Personalizar → **Banner do topo**.

**6. Comentários** — Configurações → Discussão. Recomendado para começar:
"O comentário deve ser aprovado manualmente" ligado.

## O que o tema espera de cada post

- **Imagem destacada** — é a capa do card. Sem ela o card usa uma das fotos do
  protótipo, escolhida de forma estável pelo ID. Tamanho recomendado: 1400×760.
- **Resumo** — aparece no card em destaque e abaixo do título no post.
- **Categoria** — a primeira categoria define a etiqueta e a cor.

Tempo de leitura é calculado do conteúdo, a 200 palavras por minuto.

## Estrutura

```
quality-blog/
├── style.css              CSS completo + cabeçalho do tema
├── functions.php          setup, cores por categoria, helpers, walker do menu
├── header.php             barra fixa; masthead só na home
├── footer.php
├── front-page.php         home: destaque + últimos + blocos por tema
├── index.php              listagem (também serve archive e busca)
├── archive.php            → index.php
├── search.php             → index.php
├── single.php             post
├── comments.php           comentários nativos do WP
├── searchform.php
├── 404.php
├── template-parts/
│   ├── card.php           card de post (feature / small / padrão)
│   └── mini-post.php      item dos blocos por tema
└── assets/
    ├── img/               17 imagens extraídas do protótipo
    └── js/main.js         menu mobile, acordeão, copiar link
```

## O que mudou em relação ao protótipo

- As imagens saíram do base64 e viraram arquivos (~350 KB no total). Cada página
  do protótipo pesava ~570 KB só de imagem embutida.
- A busca falsa do cabeçalho saiu; o formulário agora envia para a busca do WordPress.
- Os comentários simulados saíram; passam a ser os nativos, com fila de moderação.
- O banner do topo virou configuração no Personalizar.

## Ainda não feito

- Página de "posts salvos" para leitores logados
- `hreflang` ligando as versões EN / PT / ES
- Tradução das strings do tema (o text domain `quality-blog` já está aplicado)

## Aviso

Este tema **não foi executado em um WordPress** — a máquina onde foi escrito não tem
PHP. A sintaxe foi conferida por análise estática (delimitadores, referências de
template e de asset), mas o primeiro teste real precisa ser em **staging**, nunca
direto em produção.
