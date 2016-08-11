<?php

$GLOBALS['TL_DCA']['tl_disclaimer'] = array
(
	'config'      => array
	(
		'dataContainer'     => 'Table',
		'ptable'            => 'tl_disclaimer_archive',
		'enableVersioning'  => true,
		'onload_callback'   => array
		(
			array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'checkPermission'),
		),
		'onsubmit_callback' => array
		(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded'),
		),
		'sql'               => array
		(
			'keys' => array
			(
				'id'                       => 'primary',
				'pid,start,stop,published' => 'index',
			),
		),
	),
	'list'        => array
	(
		'label'             => array
		(
			'fields' => array('id'),
			'format' => '%s',
		),
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('title'),
			'headerFields'          => array('title', 'tstamp'),
			'panelLayout'           => 'filter;search,limit',
			'child_record_callback' => array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'listChildren'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_disclaimer']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'copy'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_disclaimer']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_disclaimer']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_disclaimer']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback' => array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'toggleIcon'),
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_disclaimer']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes'    => array(
		'__selector__' => array('source', 'addBack', 'published'),
		'default'      => '{general_legend},title,language,fallback,headline;{text_legend},text;{form_legend},acceptLabel,submitLabel,submitClass,addBack;{source_legend},source;{publish_legend},published;',
	),
	'subpalettes' => array(
		'addBack'        => 'backLabel,backClass',
		'source_page'    => 'jumpTo',
		'source_article' => 'articleId',
		'published'      => 'start,stop',
	),
	'fields'      => array
	(
		'id'          => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		),
		'pid'         => array
		(
			'foreignKey' => 'tl_disclaimer_archive.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager'),
		),
		'tstamp'      => array
		(
			'label' => &$GLOBALS['TL_LANG']['tl_disclaimer']['tstamp'],
			'sql'   => "int(10) unsigned NOT NULL default '0'",
		),
		'dateAdded'   => array
		(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		),
		'title'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['title'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'language'    => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_disclaimer']['language'],
			'exclude'          => true,
			'search'           => true,
			'sorting'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'getLanguageOptions'),
			'eval'             => array('mandatory' => true, 'tl_class' => 'w50 clr', 'includeBlankOption' => true),
			'sql'              => "varchar(255) NOT NULL default ''",
		),
		'fallback'    => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_disclaimer']['fallback'],
			'exclude'       => true,
			'inputType'     => 'checkbox',
			'eval'          => array('doNotCopy' => true, 'tl_class' => 'w50 m12'),
			'save_callback' => array
			(
				array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'checkFallback'),
			),
			'sql'           => "char(1) NOT NULL default ''",
		),
		'headline'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['headline'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'inputUnit',
			'options'   => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
			'eval'      => array('maxlength' => 200, 'tl_class' => 'clr'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'text'        => array
		(
			'label'       => &$GLOBALS['TL_LANG']['tl_disclaimer']['text'],
			'exclude'     => true,
			'inputType'   => 'textarea',
			'eval'        => array('rte' => 'tinyMCE', 'helpwizard' => true),
			'explanation' => 'insertTags',
			'sql'         => "mediumtext NULL",
		),
		'acceptLabel' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['acceptLabel'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'allowHtml' => true),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'submitLabel' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['submitLabel'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'allowHtml' => true),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'submitClass' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['submitClass'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50', 'maxlength' => 64),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'addBack'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['addBack'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'default'   => true,
			'eval'      => array('submitOnChange' => true, 'tl_class' => 'clr'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'backLabel'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['backLabel'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'allowHtml' => true),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'backClass'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['backClass'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50', 'maxlength' => 64),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'source'      => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_disclaimer']['source'],
			'default'          => 'page',
			'exclude'          => true,
			'filter'           => true,
			'inputType'        => 'radio',
			'options_callback' => array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'getSourceOptions'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_disclaimer']['reference']['source'],
			'eval'             => array('submitOnChange' => true, 'helpwizard' => true, 'mandatory' => true),
			'sql'              => "varchar(12) NOT NULL default ''",
		),
		'jumpTo'      => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_disclaimer']['jumpTo'],
			'exclude'    => true,
			'inputType'  => 'pageTree',
			'foreignKey' => 'tl_page.title',
			'eval'       => array('mandatory' => true, 'fieldType' => 'radio'),
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy'),
		),
		'articleId'   => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_disclaimer']['articleId'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Disclaimer\Backend\DisclaimerBackend', 'getArticleAlias'),
			'eval'             => array('chosen' => true, 'mandatory' => true),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'published'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['published'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('doNotCopy' => true, 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'start'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['start'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'stop'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_disclaimer']['stop'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
	),
);
