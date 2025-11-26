#!/usr/bin/env bash
set -euo pipefail

# Script pour migrer automatiquement toutes les entités restantes vers UUID
# Entités déjà migrées: User, Role, Subject, Series, ResourceType, Tag

ENTITIES=(
  "Chapter"
  "Resource"
  "ResourceTag"
  "ResourceRating"
  "SeriesSubject"
  "ExamPaper"
  "Solution"
  "RevisionSheet"
  "Exercise"
  "UserProgress"
  "PracticeSession"
  "ForumCategory"
  "ForumTopic"
  "ForumPost"
)

BACKEND_PATH="/home/florian/Bureau/Codes/wiki-bac-bj/backend/src/Entity"

echo "[INFO] Migration de ${#ENTITIES[@]} entités vers UUID..."

for ENTITY in "${ENTITIES[@]}"; do
  FILE="$BACKEND_PATH/$ENTITY.php"
  
  if [ ! -f "$FILE" ]; then
    echo "[WARN] $FILE introuvable, ignoré."
    continue
  fi
  
  echo "[PROCESSING] $ENTITY..."
  
  # Backup
  cp "$FILE" "$FILE.bak"
  
  # 1. Ajouter les imports UUID si absents
  if ! grep -q "use Symfony\\\\Component\\\\Uid\\\\Uuid;" "$FILE"; then
    sed -i '/^use Doctrine\\ORM\\Mapping as ORM;/a use Symfony\\Component\\Uid\\Uuid;' "$FILE"
  fi
  
  if ! grep -q "use App\\\\Entity\\\\Trait\\\\UuidPrimaryKey;" "$FILE"; then
    sed -i '/^namespace App\\Entity;/a \\nuse App\\Entity\\Trait\\UuidPrimaryKey;' "$FILE"
  fi
  
  # 2. Remplacer l'ID int par le trait UUID
  sed -i '/#\[ORM\\Id\]/,/private ?\(int\|string\) \$id = null;/c\    use UuidPrimaryKey;' "$FILE"
  
  # 3. Mettre à jour constructeur (ajouter $this->id = Uuid::v7();)
  # Trouver la ligne après "public function __construct()" et ajouter l'init UUID
  sed -i '/public function __construct()/a\        $this->id = Uuid::v7();' "$FILE"
  
  # 4. Mettre à jour getId()
  sed -i 's/public function getId(): ?\(int\|string\)/public function getId(): ?Uuid/' "$FILE"
  
  echo "[OK] $ENTITY migré."
done

echo "[INFO] Migration terminée. Vérifie les fichiers et supprime les .bak si tout est OK."
echo "[INFO] Prochaines étapes:"
echo "  1. docker compose exec backend php bin/console doctrine:schema:validate"
echo "  2. docker compose exec backend php bin/console doctrine:migrations:diff"
echo "  3. docker compose exec backend php bin/console doctrine:migrations:migrate"
