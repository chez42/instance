<?php
function Documents_CreateReminder($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];
	$parentid = $entityData->get("related_to");
	$adb->pquery("insert into vtiger_documents_reminder_popup(creatorid, recordid, status) values(?,?,?)",array($parentid, $entityId, "0"));
}