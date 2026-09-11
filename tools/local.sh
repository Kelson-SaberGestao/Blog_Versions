#!/usr/bin/env bash
#
# Atalho para rodar os scripts de setup num site do Local (localwp.com).
#
# Descobre sozinho o PHP do Local, a pasta do site e o socket do MySQL, que e
# a parte chata de acertar na mao.
#
# Uso:
#   ./tools/local.sh setup-site              # configura o site
#   ./tools/local.sh setup-menu              # monta os menus em ingles
#   ./tools/local.sh setup-menu es           # monta os menus em espanhol
#   ./tools/local.sh setup-site meu-site     # se tiver mais de um site no Local
#
# Em servidor de verdade nao use este atalho: la e so
#   WP_PUBLIC=/caminho/do/wordpress php tools/setup-site.php

set -euo pipefail

SCRIPT="${1:-}"
ARG="${2:-}"

if [ -z "$SCRIPT" ]; then
  echo "uso: ./tools/local.sh setup-site|setup-menu [es|nome-do-site]" >&2
  exit 1
fi

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_FILE="$ROOT/tools/$SCRIPT.php"
[ -f "$PHP_FILE" ] || { echo "nao existe: tools/$SCRIPT.php" >&2; exit 1; }

# 'es' e idioma; qualquer outra coisa e nome de site
LANG_ARG=""
SITE_NAME=""
case "$ARG" in
  es|ES) LANG_ARG="es" ;;
  "")    ;;
  *)     SITE_NAME="$ARG" ;;
esac

SUPPORT="$HOME/Library/Application Support/Local"

# --- PHP do Local (o mais recente) ---
PHP="$(find "$SUPPORT/lightning-services" -type f -perm -u+x -name php 2>/dev/null | sort -V | tail -1 || true)"
[ -n "$PHP" ] || { echo "PHP do Local nao encontrado. O Local esta instalado?" >&2; exit 1; }

# --- pasta do site ---
if [ -n "$SITE_NAME" ]; then
  PUBLIC="$HOME/Local Sites/$SITE_NAME/app/public"
else
  PUBLIC="$(find "$HOME/Local Sites" -maxdepth 3 -type d -name public -path '*/app/public' 2>/dev/null | head -1 || true)"
fi
[ -f "$PUBLIC/wp-load.php" ] || { echo "WordPress nao encontrado em: $PUBLIC" >&2; exit 1; }

# --- socket do MySQL (o site precisa estar rodando) ---
SOCKET="$(find "$SUPPORT/run" -name 'mysqld.sock' 2>/dev/null | head -1 || true)"
[ -n "$SOCKET" ] || { echo "MySQL nao esta no ar. Inicie o site no Local e tente de novo." >&2; exit 1; }

echo "site  : $PUBLIC"
echo "php   : $("$PHP" -r 'echo PHP_VERSION;')"
[ -n "$LANG_ARG" ] && echo "idioma: $LANG_ARG"
echo

# O socket entra pelo ini do PHP, nao redefinindo DB_HOST por cima do
# wp-config - redefinir constante emite warning e suja a saida.
WP_PUBLIC="$PUBLIC" LANG="$LANG_ARG" \
  "$PHP" -d mysqli.default_socket="$SOCKET" -d pdo_mysql.default_socket="$SOCKET" "$PHP_FILE"
