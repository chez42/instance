<?php

/**
 * Defines a list of folder identifiers.
 *
 * @package php-ews\Array
 */
class EWSType_ArrayOfFolderIdType extends EWSType
{
    /**
     * Contains the identifier and change key of a folder.
     *
     * @since Exchange 2013
     *
     * @var \jamesiarmes\PhpEws\Type\FolderIdType[]
     */
    public $FolderId = array();
}
