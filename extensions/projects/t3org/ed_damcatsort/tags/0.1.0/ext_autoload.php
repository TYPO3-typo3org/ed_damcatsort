<?php

$extensionClassesPath = t3lib_extMgm::extPath('ed_damcatsort') . 'Classes/';
return array(
	'tx_eddamcatsort_domain_model_dam' => $extensionClassesPath . 'Domain/Model/Dam.php',
	'tx_eddamcatsort_domain_model_damcategory' => $extensionClassesPath . 'Domain/Model/DamCategory.php',
	'tx_eddamcatsort_domain_model_media' => $extensionClassesPath . 'Domain/Model/Media.php',
	'tx_eddamcatsort_domain_model_damrepository' => $extensionClassesPath . 'Domain/Repository/DamRepository.php',
	'tx_eddamcatsort_domain_model_damcategoryrepository' => $extensionClassesPath . 'Domain/Repository/DamCategoryRepository.php',
	'tx_eddamcatsort_domain_model_mediarepository' => $extensionClassesPath . 'Domain/Repository/MediaRepository.php',
);

?>