import fontforge
import os
import re

# 📂 Répertoires
INPUT_DIR = "../images/icons/icons_raw"
CLEAN_DIR = "../images/icons/icons_clean"
OUTPUT_FONT = "../fonts/icons.ttf"
OUTPUT_CSS = "../css/icons.css"
PREFIX = "tade"

# Créer dossier clean si besoin
if not os.path.exists(CLEAN_DIR):
    os.makedirs(CLEAN_DIR)

if os.path.exists(OUTPUT_FONT):
    os.remove(OUTPUT_FONT)

if os.path.exists(OUTPUT_CSS):
    os.remove(OUTPUT_CSS)

# Nettoyage SVG (viewBox + suppression fill)
for filename in os.listdir(INPUT_DIR):
    if filename.endswith(".svg"):
        with open(os.path.join(INPUT_DIR, filename), "r", encoding="utf-8") as f:
            content = f.read()

        # Ajouter un viewBox si absent
        if "viewBox" not in content:
            content = content.replace("<svg ", '<svg viewBox="0 0 24 24" ', 1)

        # Supprimer les attributs fill="..."
        content = re.sub(r'fill="[^"]*"', '', content)

        with open(os.path.join(CLEAN_DIR, filename), "w", encoding="utf-8") as f:
            f.write(content)

        print(f"✔ {filename} nettoyé")

font = fontforge.font()
font.encoding = "UnicodeFull"
font.fontname = "TadeFont"
font.fullname = "TaderLafe Font"
font.familyname = "TadeFont"

start_codepoint = 0xE001
css_lines = [
    "@font-face {",
    "  font-family: 'TadeFont';",
    "  src: url('/assets/fonts/icons.ttf') format('truetype');",
    "  font-weight: normal;",
    "  font-style: normal;",
    "}"
]

codepoint = start_codepoint

glyph_count = 0

for file in os.listdir(CLEAN_DIR):
    if file.endswith(".svg"):
        glyph_count += 1
        name = os.path.splitext(file)[0]
        glyph = font.createChar(codepoint, name)
        glyph.importOutlines(os.path.join(CLEAN_DIR, file))
        glyph.width = 1000
        css_lines.append(f".{PREFIX}-{name}:before {{ content: '\\{codepoint:X}'; }}")
        codepoint += 1

if glyph_count == 0:
    print("❌ Aucun fichier SVG trouvé → police non générée.")
    exit(1)

# Génération police + CSS
font.generate(OUTPUT_FONT)

with open(OUTPUT_CSS, "w") as f:
    f.write("\n".join(css_lines))

print("✅ Font générée :", OUTPUT_FONT)
print("✅ CSS généré :", OUTPUT_CSS)