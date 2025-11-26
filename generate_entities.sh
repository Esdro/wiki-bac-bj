#!/usr/bin/env bash
set -euo pipefail

# Script d'aide pour enchaîner les commandes Symfony Maker "make:entity".
# Utilisation:
#   ./generate_entities.sh            -> lance interactif pour chaque entité (docker compose requis)
#   ./generate_entities.sh --no-rel   -> saute les entités dépendantes des relations complexes
#   ./generate_entities.sh --empty    -> utilise --no-interaction (crée des entités vides que tu complèteras ensuite)
#
# Pré-requis:
#   - Docker & docker compose
#   - Service "backend" défini dans docker-compose.yml
#   - MakerBundle installé (déjà présent: symfony/maker-bundle)
#
# Remarques:
#   - Le mode interactif te demandera les champs; appuie Enter si tu veux terminer une entité sans champ.
#   - L'option --empty crée uniquement les classes d'entités (sans propriétés); relance ensuite make:entity Nom pour ajouter des champs.
#   - ForumTopic a une relation lastPost vers ForumPost; crée d'abord ForumPost puis ajoute la relation manuellement.

NO_REL=false
EMPTY=false

for arg in "$@"; do
  case "$arg" in
    --no-rel) NO_REL=true ;;
    --empty) EMPTY=true ;;
    *) echo "Option inconnue: $arg"; exit 1 ;;
  esac
done

# Liste des entités de base correspondant au schéma SQL.
BASE_ENTITIES=(
  Role
  User
  Subject
  Series
  SeriesSubject
  Chapter
  ResourceType
  Resource
  Tag
  ResourceTag
  ResourceRating
  ExamPaper
  Solution
  RevisionSheet
  Exercise
  UserProgress
  PracticeSession
  ForumCategory
  ForumTopic
  ForumPost
)

if $NO_REL; then
  # Exclure les tables pivot / spécifiques si demandé
  BASE_ENTITIES=(Role User Subject Series Chapter ResourceType Resource Tag ResourceRating ForumCategory ForumTopic ForumPost)
fi

echo "[INFO] Démarrage génération des entités (${#BASE_ENTITIES[@]})." 
echo "[INFO] Mode: $([ "$EMPTY" = true ] && echo 'ENTITIES VIDES (--no-interaction)' || echo 'INTERACTIF')."

# Vérifier docker compose backend
if ! docker compose ps backend >/dev/null 2>&1; then
  echo "[ERREUR] Service 'backend' introuvable. Lance 'docker compose up -d backend' avant." >&2
  exit 1
fi

# Test accès console Symfony
if ! docker compose exec backend php bin/console list >/dev/null 2>&1; then
  echo "[ERREUR] Impossible d'exécuter 'php bin/console' dans le conteneur backend." >&2
  exit 1
fi

for ENTITY in "${BASE_ENTITIES[@]}"; do
  echo "=============================================="
  echo "[GEN] Entité: $ENTITY"
  if $EMPTY; then
    docker compose exec -T backend php bin/console make:entity "$ENTITY" --no-interaction || {
      echo "[WARN] make:entity --no-interaction a échoué pour $ENTITY" >&2
    }
  else
    echo "[INFO] Lancement interactif. Ajoute les champs conformément au schéma ou Enter pour terminer."
    docker compose exec backend php bin/console make:entity "$ENTITY"
  fi
done

echo "=============================================="
echo "[INFO] Génération terminée. Étapes suivantes suggérées:"
echo "  1. Ajouter/compléter les relations manquantes (ex: lastPost dans ForumTopic)."
echo "  2. Lancer: docker compose exec backend php bin/console doctrine:schema:validate"
echo "  3. Puis: docker compose exec backend php bin/console doctrine:migrations:diff"
echo "  4. Et: docker compose exec backend php bin/console doctrine:migrations:migrate"
echo "  5. Ajouter des constantes pour les pseudo-enums (status, type, etc.)."

exit 0