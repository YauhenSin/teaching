#!/bin/bash

doc="vendor/bin/doctrine-module"

folderUp2="../../"
templatemapGeneratorPath=$folderUp2"vendor/zendframework/zendframework/bin/templatemap_generator.php"

case "$1" in
    generate-entities)
        echo "[$1] Generating >>> " & $doc orm:generate:entities module/Core/src
        ;;
    validate-schema)
        echo "[$1] Validating >>> " & $doc orm:validate-schema
        ;;
    update-schema)
        echo "[$1] Updating >>> " & $doc orm:schema-tool:update --force
        ;;
    generate-templates)
        echo "[$1] Generating >>> "
        cd module/Application && $templatemapGeneratorPath && cd $folderUp2
        cd module/Core && $templatemapGeneratorPath && cd $folderUp2
        cd module/Superadmin && $templatemapGeneratorPath && cd $folderUp2
        cd module/Admin && $templatemapGeneratorPath && cd $folderUp2
        ;;
    *)
        echo $"Usage: $0 {generate-entities|validate-schema|update-schema|generate-templates|import:fixtures}"
        exit 1
esac
echo "[$1] Done <<<"