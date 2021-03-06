<?php

/**
 * Copyright notice
 *
 * (c) 2016 Daniel Haring <development@haring.co.at>
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

\defined('TYPO3_MODE') or die('Access denied.');

$_EXTCONF = \unserialize($_EXTCONF);




    // Adjust core setup
$additionalRootLineFields = \array_filter(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
        ',', 
        $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields']));

\array_push($additionalRootLineFields, 'nav_title', 'subtitle');

$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = \implode(', ', \array_unique($additionalRootLineFields));
unset($additionalRootLineFields);





    // Register Icons
/* @var $IconRegistry \TYPO3\CMS\Core\Imaging\IconRegistry */
$IconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class);

$IconRegistry->registerIcon(
        'content-plugin-video', 
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, 
        [
            'source' => 'EXT:crystalis/Resources/Public/Icons/content-video.svg'
        ]);





    // Inject TypoScript configurations
if($_EXTCONF['setUserTSconfig'] || !$_EXTCONF) {
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:crystalis/Configuration/TypoScript/Backend/UserRights.ts">');
    
}

if(
        (\in_array($_EXTCONF['doctype'], ['html5']) && !$_EXTCONF['disableHtml5Video']) 
        || !$_EXTCONF) {
    
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:crystalis/Configuration/TypoScript/ContentElements/Html5Video.ts">');
    
}




    // Register content rendering templates
\settype($GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'], 'array');

switch($_EXTCONF['doctype']) {
    
    case 'xhtml_strict':
        
        $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] 
                = 'crystalis/Configuration/TypoScript/Controller/XHtmlStrict/';
        
        break;
    
    case 'html5': // fallthrough
    default:
        
        $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'][] 
                = 'crystalis/Configuration/TypoScript/Controller/Html5/';
        
        break;
    
}





    // Register Content Objects
if(\TYPO3_MODE === 'FE') {
    
    $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['VIDEO'] 
            = \DanielHaring\Crystalis\ContentObject\Html5VideoContentObject::class;
    
}





    // Automatic language handling
if(!!$_EXTCONF['enableLanguageHandling'] || !$_EXTCONF) {
    
    /* @var $LanguageService \DanielHaring\Crystalis\Service\LanguageService */
    $LanguageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \DanielHaring\Crystalis\Service\LanguageService::class);
    
    if($typoscript = $LanguageService->getTypoScriptSetup()) {
        
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup($typoscript);
        
    }
    
    if($pageTS = $LanguageService->getPageTSConfig()) {
        
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig($pageTS);
        
    }
    
    $LanguageService->prepareUrlRewriting();
    
}





    // Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['text'] = 
        \DanielHaring\Crystalis\Hooks\PageLayoutView\TextPreviewRenderer::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['image'] = 
        \DanielHaring\Crystalis\Hooks\PageLayoutView\ImagePreviewRenderer::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['textpic'] = 
        \DanielHaring\Crystalis\Hooks\PageLayoutView\TextpicPreviewRenderer::class;