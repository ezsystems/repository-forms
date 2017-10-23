#!/usr/bin/env sh
echo 'Translation extraction';
cd ../../..;
# Extract string for default locale
echo '# Extract RepositoryForms';
./app/console translation:extract en -v \
  --dir=./vendor/ezsystems/repository-forms/bundle \
  --dir=./vendor/ezsystems/repository-forms/lib \
  --exclude-dir=vendor \
  --output-dir=./vendor/ezsystems/repository-forms/bundle/Resources/translations \
  --enable-extractor=ez_location_sorting \
  --enable-extractor=ez_policy_limitation \
  --keep
  "$@"

echo '# Clean file references';
sed -i "s|>.*/vendor/ezsystems/repository-forms/|>|g" ./vendor/ezsystems/repository-forms/bundle/Resources/translations/*.xlf

cd vendor/ezsystems/repository-forms;
echo 'Translation extraction done';
