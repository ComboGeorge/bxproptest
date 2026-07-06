<?php

class CIBlockPropertyAjaxScheme
{
	public const USER_TYPE = 'ajax_image';
	public const PREVIEW_MAX_SIDE = 2000;
	public const MAX_POINTS = 50;
	public const MAX_ELEMENTS_PER_POINT = 30;
	public const MAX_POINT_TITLE_LEN = 255;
	public const MAX_POINT_FACTORY_NUMBER_LEN = 100;
	public const MAX_POINT_QUANTITY_LEN = 50;

	public static function GetUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => self::USER_TYPE,
			'DESCRIPTION' => "\xD0\xA1\xD1\x85\xD0\xB5\xD0\xBC\xD0\xB0 \xD1\x81 \xD0\xB4\xD0\xB0\xD0\xBD\xD0\xBD\xD1\x8B\xD0\xBC\xD0\xB8",
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
			'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
			'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__, 'PrepareSettings'),
			'CheckFields' => array(__CLASS__, 'CheckFields'),
		);
	}

	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'FILTRABLE'),
			'USER_TYPE_SETTINGS_TITLE' => "\xD0\x9D\xD0\xB0\xD1\x81\xD1\x82\xD1\x80\xD0\xBE\xD0\xB9\xD0\xBA\xD0\xB8 \xD1\x81\xD1\x85\xD0\xB5\xD0\xBC\xD1\x8B \xD1\x81 \xD0\xB4\xD0\xB0\xD0\xBD\xD0\xBD\xD1\x8B\xD0\xBC\xD0\xB8",
		);

		$settings = self::getPropertySettings($arProperty);
		$linkIblockId = (int)($settings['LINK_IBLOCK_ID'] ?? 0);
		$fieldName = htmlspecialcharsbx((string)($strHTMLControlName['NAME'] ?? 'USER_TYPE_SETTINGS'));

		$html = '<tr><td width="40%">'
			. "\xD0\x98\xD0\xBD\xD1\x84\xD0\xBE\xD0\xB1\xD0\xBB\xD0\xBE\xD0\xBA \xD0\xB4\xD0\xBB\xD1\x8F \xD0\xBF\xD1\x80\xD0\xB8\xD0\xB2\xD1\x8F\xD0\xB7\xD0\xBA\xD0\xB8 \xD1\x8D\xD0\xBB\xD0\xB5\xD0\xBC\xD0\xB5\xD0\xBD\xD1\x82\xD0\xBE\xD0\xB2:"
			. '</td><td width="60%">';

		if (\Bitrix\Main\Loader::includeModule('iblock'))
		{
			$html .= '<select name="' . $fieldName . '[LINK_IBLOCK_ID]">';
			$html .= '<option value="0">— ' . "\xD0\xBD\xD0\xB5 \xD0\xB2\xD1\x8B\xD0\xB1\xD1\x80\xD0\xB0\xD0\xBD" . ' —</option>';
			$rs = CIBlock::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'), array('ACTIVE' => 'Y'));
			while ($iblock = $rs->Fetch())
			{
				$id = (int)$iblock['ID'];
				$label = '[' . $id . '] ' . (string)$iblock['NAME'];
				$selected = ($linkIblockId === $id) ? ' selected' : '';
				$html .= '<option value="' . $id . '"' . $selected . '>' . htmlspecialcharsbx($label) . '</option>';
			}
			$html .= '</select>';
		}
		else
		{
			$html .= '<input type="number" min="0" step="1" name="' . $fieldName . '[LINK_IBLOCK_ID]" value="' . $linkIblockId . '">';
		}

		$html .= '<div style="color:#6a737d;font-size:12px;margin-top:4px;">'
			. "\xD0\x98\xD1\x81\xD0\xBF\xD0\xBE\xD0\xBB\xD1\x8C\xD0\xB7\xD1\x83\xD0\xB5\xD1\x82\xD1\x81\xD1\x8F \xD0\xBF\xD1\x80\xD0\xB8 \xD0\xB2\xD1\x8B\xD0\xB1\xD0\xBE\xD1\x80\xD0\xB5 \xD1\x8D\xD0\xBB\xD0\xB5\xD0\xBC\xD0\xB5\xD0\xBD\xD1\x82\xD0\xB0 \xD0\xBA \xD1\x82\xD0\xBE\xD1\x87\xD0\xBA\xD0\xB5 \xD0\xBD\xD0\xB0 \xD0\xBA\xD0\xB0\xD1\x80\xD1\x82\xD0\xB8\xD0\xBD\xD0\xBA\xD0\xB5."
			. '</div>';
		$html .= '</td></tr>';

		return $html;
	}

	public static function PrepareSettings($arFields)
	{
		$settings = array();
		if (isset($arFields['USER_TYPE_SETTINGS']) && is_array($arFields['USER_TYPE_SETTINGS']))
		{
			$settings = $arFields['USER_TYPE_SETTINGS'];
		}

		return array(
			'LINK_IBLOCK_ID' => (int)($settings['LINK_IBLOCK_ID'] ?? 0),
		);
	}

	private static function getPropertySettings($arProperty)
	{
		if (is_array($arProperty['USER_TYPE_SETTINGS'] ?? null))
		{
			return $arProperty['USER_TYPE_SETTINGS'];
		}

		$raw = $arProperty['USER_TYPE_SETTINGS'] ?? '';
		if (is_string($raw) && $raw !== '')
		{
			$unserialized = @unserialize($raw, array('allowed_classes' => false));
			if (is_array($unserialized))
			{
				return $unserialized;
			}
		}

		return array();
	}

	private static function getLinkIblockId($arProperty)
	{
		$settings = self::getPropertySettings($arProperty);

		return (int)($settings['LINK_IBLOCK_ID'] ?? 0);
	}

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		return self::renderBlock($arProperty, $value, $strHTMLControlName);
	}

	public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
	{
		return self::renderBlock($arProperty, $value, $strHTMLControlName);
	}

	public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$fileId = self::valueToFileId($value);
		if ($fileId <= 0)
		{
			return '&mdash;';
		}

		return CFile::ShowImage($fileId, 50, 50, 'border="0" style="vertical-align:middle;"', '', true);
	}

	public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$fileId = self::valueToFileId($value);
		if ($fileId <= 0)
		{
			return '';
		}

		$src = CFile::GetPath($fileId);
		if (!$src)
		{
			return '';
		}

		return '<img src="' . htmlspecialcharsbx($src) . '" alt="" class="ajax-img-preview-img" style="' . self::getPreviewCss() . '">';
	}

	public static function ConvertFromDB($arProperty, $arValue)
	{
		$linkIblockId = self::getLinkIblockId($arProperty);
		$payload = self::unpackPropertyValue($arValue, $linkIblockId);

		$arValue['VALUE'] = self::encodeValuePayload($payload['file_id'], $payload['points'], $linkIblockId);
		$arValue['DESCRIPTION'] = '';

		return $arValue;
	}

	public static function ConvertToDB($arProperty, $arValue)
	{
		$propId = (int)($arProperty['ID'] ?? 0);
		$iblockId = (int)($arProperty['IBLOCK_ID'] ?? 0);
		$elementId = self::resolveElementIdFromRequest();
		$linkIblockId = self::getLinkIblockId($arProperty);

		$payload = self::resolveValuePayloadFromRequest($arProperty, $arValue);
		$fileId = (int)$payload['file_id'];

		if ($fileId <= 0)
		{
			$fileId = self::restoreFileIdFromRequest($arProperty);
		}

		if ($fileId <= 0)
		{
			$fileId = self::getPendingFileId($elementId, $propId);
		}

		if ($fileId <= 0 && $elementId > 0 && $propId > 0)
		{
			$fileId = self::getStoredFileIdForElement($iblockId, $elementId, $propId);
		}

		if ($fileId > 0 && empty($payload['points']) && $elementId > 0 && $propId > 0 && $iblockId > 0)
		{
			$stored = self::getStoredValuePayloadForElement($iblockId, $elementId, $propId);
			if (!empty($stored['points']))
			{
				$payload['points'] = $stored['points'];
			}
		}

		if (self::isDeleteFlagged($arProperty))
		{
			if ($fileId > 0)
			{
				CFile::Delete($fileId);
			}
			self::clearPendingFileId($elementId, $propId);

			return array(
				'VALUE' => false,
				'DESCRIPTION' => '',
			);
		}

		if ($fileId <= 0)
		{
			if ($iblockId <= 0)
			{
				$iblockId = (int)($_POST['IBLOCK_ID'] ?? $_REQUEST['IBLOCK_ID'] ?? 0);
			}

			if ($elementId > 0 && $propId > 0 && $iblockId > 0)
			{
				$existing = self::getStoredFileIdForElement($iblockId, $elementId, $propId);
				if ($existing > 0)
				{
					$fileId = $existing;
				}
			}

			if ($fileId <= 0)
			{
				return array(
					'VALUE' => self::encodeValuePayload(0, $payload['points'], $linkIblockId),
					'DESCRIPTION' => '',
				);
			}
		}

		self::setPendingFileId($elementId, $propId, $fileId);

		return array(
			'VALUE' => self::encodeValuePayload($fileId, $payload['points'], $linkIblockId),
			'DESCRIPTION' => '',
		);
	}

	public static function persistPropertiesFromPost($elementId, $iblockId, $onlyPending = false)
	{
		$elementId = (int)$elementId;
		$iblockId = (int)$iblockId;
		$onlyPending = ($onlyPending === true);

		if ($elementId <= 0 || $iblockId <= 0)
		{
			return;
		}

		if (!\Bitrix\Main\Loader::includeModule('iblock'))
		{
			return;
		}

		$fileIds = self::filterFileIdsForAjaxImageProps($iblockId, self::collectAllFileIdsFromPost($iblockId));

		foreach (self::getAjaxImageProperties($iblockId) as $propId => $prop)
		{
			$propId = (int)$propId;
			$propCode = (string)($prop['CODE'] ?? '');

			if (self::isDeleteFlagged($prop))
			{
				self::writePropertyValue($elementId, $iblockId, $propId, $propCode, false);
				self::clearPendingFileId($elementId, $propId);
				unset($fileIds[$propId]);
				continue;
			}

			if (!isset($fileIds[$propId]) || (int)$fileIds[$propId] <= 0)
			{
				$pending = self::getPendingFileId($elementId, $propId);
				if ($pending > 0)
				{
					$fileIds[$propId] = $pending;
				}
			}
		}

		$fileIds = self::filterFileIdsForAjaxImageProps($iblockId, $fileIds);

		foreach ($fileIds as $propId => $fileId)
		{
			if ((int)$fileId <= 0)
			{
				continue;
			}

			$propId = (int)$propId;
			$prop = self::getAjaxImageProperties($iblockId)[$propId] ?? null;
			if (!is_array($prop))
			{
				continue;
			}

			if ($onlyPending && self::getPendingFileId($elementId, $propId) <= 0)
			{
				continue;
			}

			$propCode = (string)($prop['CODE'] ?? '');
			$linkIblockId = self::getLinkIblockId($prop);
			$payload = self::resolveValuePayloadFromRequest($prop, array());
			$payload = self::mergePayloadWithStoredPoints($iblockId, $elementId, $propId, $payload);
			$points = is_array($payload['points'] ?? null) ? $payload['points'] : array();

			if (!$onlyPending && empty($points))
			{
				continue;
			}

			self::writePropertyValue($elementId, $iblockId, $propId, $propCode, (int)$fileId, $points);
			self::clearPendingFileId($elementId, $propId);
		}
	}

	public static function onBeforeElementSave(&$arFields)
	{
		$iblockId = (int)($arFields['IBLOCK_ID'] ?? $_POST['IBLOCK_ID'] ?? 0);
		if ($iblockId <= 0)
		{
			return;
		}

		$elementId = self::resolveElementIdFromRequest();
		$fileIds = self::filterFileIdsForAjaxImageProps($iblockId, self::collectAllFileIdsFromPost($iblockId));

		foreach (self::getAjaxImageProperties($iblockId) as $propId => $prop)
		{
			$propId = (int)$propId;
			if (isset($fileIds[$propId]) && (int)$fileIds[$propId] > 0)
			{
				continue;
			}
			if (self::isDeleteFlagged($prop))
			{
				continue;
			}
			$pending = self::getPendingFileId($elementId, $propId);
			if ($pending > 0)
			{
				$fileIds[$propId] = $pending;
			}
		}

		$fileIds = self::filterFileIdsForAjaxImageProps($iblockId, $fileIds);
		if (empty($fileIds))
		{
			return;
		}

		if (!is_array($arFields['PROPERTY_VALUES'] ?? null))
		{
			$arFields['PROPERTY_VALUES'] = array();
		}

		$elementId = self::resolveElementIdFromRequest();
		foreach ($fileIds as $propId => $fileId)
		{
			$propId = (int)$propId;
			$prop = self::getAjaxImageProperties($iblockId)[$propId] ?? null;
			if (!is_array($prop))
			{
				continue;
			}

			$rowKey = self::detectRowKeyFromPost($propId) ?? 'n0';
			$linkIblockId = self::getLinkIblockId($prop);
			$payload = self::resolveValuePayloadFromRequest($prop, array());
			$payload = self::mergePayloadWithStoredPoints($iblockId, $elementId, $propId, $payload);

			$arFields['PROPERTY_VALUES'][$propId][$rowKey] = array(
				'VALUE' => self::encodeValuePayload((int)$fileId, $payload['points'], $linkIblockId),
				'DESCRIPTION' => '',
			);
		}
	}

	public static function onAfterElementSave(&$arFields)
	{
		$elementId = (int)($arFields['ID'] ?? 0);
		$iblockId = (int)($arFields['IBLOCK_ID'] ?? 0);

		if ($elementId <= 0)
		{
			$elementId = (int)($_POST['ID'] ?? $_POST['id'] ?? $_REQUEST['ID'] ?? 0);
		}
		if ($iblockId <= 0)
		{
			$iblockId = (int)($_POST['IBLOCK_ID'] ?? $_REQUEST['IBLOCK_ID'] ?? 0);
		}

		// Основное сохранение — ConvertToDB / onBeforeElementSave. Здесь только AJAX pending.
		if ($elementId > 0 && $iblockId > 0)
		{
			self::persistPropertiesFromPost($elementId, $iblockId, true);
		}
	}

	public static function schedulePersistFromPost($elementId, $iblockId)
	{
		$elementId = (int)$elementId;
		$iblockId = (int)$iblockId;
		if ($elementId <= 0 || $iblockId <= 0)
		{
			return;
		}

		register_shutdown_function(static function () use ($elementId, $iblockId) {
			CIBlockPropertyAjaxScheme::persistPropertiesFromPost($elementId, $iblockId, true);
		});
	}

	public static function onBeforePrologPersist()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			return;
		}

		$isElementSave = !empty($_POST['save']) || !empty($_POST['apply']) || !empty($_POST['Apply'])
			|| !empty($_POST['Update']) || !empty($_POST['btn']) || !empty($_POST['from_module'])
			|| (isset($_POST['action']) && in_array((string)$_POST['action'], array('save', 'apply'), true));

		if (!$isElementSave && empty($_POST['PROP']))
		{
			return;
		}

		$elementId = (int)($_POST['ID'] ?? $_POST['id'] ?? $_REQUEST['ID'] ?? 0);
		$iblockId = (int)($_POST['IBLOCK_ID'] ?? $_REQUEST['IBLOCK_ID'] ?? 0);
		if ($elementId > 0 && $iblockId > 0)
		{
			self::schedulePersistFromPost($elementId, $iblockId);
		}
	}

	public static function onAfterEpilogPersist()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			return;
		}

		$elementId = (int)($_POST['ID'] ?? $_POST['id'] ?? $_REQUEST['ID'] ?? 0);
		$iblockId = (int)($_POST['IBLOCK_ID'] ?? $_REQUEST['IBLOCK_ID'] ?? 0);
		if ($elementId > 0 && $iblockId > 0)
		{
			self::persistPropertiesFromPost($elementId, $iblockId, true);
		}
	}

	public static function CheckFields($arProperty, $arValue)
	{
		return array();
	}

	public static function ajaxUpload($request)
	{
		if (!is_object($request))
		{
			$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		}

		if (!\Bitrix\Main\Loader::includeModule('main'))
		{
			return self::ajaxError('Module main is not installed');
		}

		\Bitrix\Main\Loader::includeModule('iblock');

		$oldFileId = (int)$request->getPost('old_file_id');
		$file = self::collectUploadedFile($request);

		if ($file === null)
		{
			return self::ajaxError(self::uploadErrorMessage(UPLOAD_ERR_NO_FILE));
		}

		$errorCode = (int)($file['error'] ?? UPLOAD_ERR_OK);
		if ($errorCode !== UPLOAD_ERR_OK)
		{
			return self::ajaxError(self::uploadErrorMessage($errorCode));
		}

		if (empty($file['tmp_name']) || !is_readable($file['tmp_name']))
		{
			return self::ajaxError('Temp file is missing');
		}

		if (!is_uploaded_file($file['tmp_name']))
		{
			$prepared = CFile::MakeFileArray($file['tmp_name']);
			if (!is_array($prepared))
			{
				return self::ajaxError('Failed to prepare file');
			}
			$prepared['name'] = (string)($file['name'] ?? $prepared['name'] ?? 'image.jpg');
			$prepared['type'] = (string)($file['type'] ?? $prepared['type'] ?? 'image/jpeg');
			$file = $prepared;
		}
		else
		{
			$check = CFile::CheckImageFile($file, 0, 0, 0);
			if ($check !== '' && $check !== null && $check !== false)
			{
				return self::ajaxError($check);
			}
		}

		$file['MODULE_ID'] = 'iblock';
		$newFileId = (int)CFile::SaveFile($file, 'iblock');
		if ($newFileId <= 0)
		{
			global $APPLICATION;
			$message = 'CFile::SaveFile failed';
			if (is_object($APPLICATION))
			{
				$exception = $APPLICATION->GetException();
				if ($exception)
				{
					$exceptionMessage = $exception->GetString();
					if (is_array($exceptionMessage))
					{
						$exceptionMessage = implode('. ', $exceptionMessage);
					}
					$message = trim((string)$exceptionMessage) ?: $message;
					$APPLICATION->ResetException();
				}
			}

			return self::ajaxError($message);
		}

		if ($oldFileId > 0 && $oldFileId !== $newFileId)
		{
			CFile::Delete($oldFileId);
		}

		$src = self::getPreviewSrc($newFileId);
		if ($src === '')
		{
			return self::ajaxError('File saved (id ' . $newFileId . ') but path is empty');
		}

		$previewHtml = self::getPreviewHtml($newFileId);

		$elementId = (int)$request->getPost('element_id');
		$iblockId = (int)$request->getPost('iblock_id');
		$propId = (int)$request->getPost('property_id');
		$propCode = trim((string)$request->getPost('property_code'));

		$propertySaved = false;
		if ($elementId > 0 && $iblockId > 0 && $propId > 0)
		{
			self::writePropertyValue($elementId, $iblockId, $propId, $propCode, $newFileId);
			self::setPendingFileId($elementId, $propId, $newFileId);
			$propertySaved = true;
		}
		elseif ($propId > 0)
		{
			self::setPendingFileId(0, $propId, $newFileId);
		}

		return self::ajaxSuccess(array(
			'file_id' => $newFileId,
			'src' => $src,
			'preview_html' => $previewHtml,
			'property_saved' => $propertySaved,
		));
	}

	private static function ajaxError($message)
	{
		$message = trim((string)$message);
		if ($message === '')
		{
			$message = 'Unknown error';
		}

		return array(
			'success' => false,
			'error' => $message,
		);
	}

	private static function ajaxSuccess(array $data)
	{
		$data['success'] = true;

		return $data;
	}

	private static function collectUploadedFile($request)
	{
		$file = null;

		if (is_object($request) && method_exists($request, 'getFile'))
		{
			$file = $request->getFile('file');
		}

		if ((!is_array($file) || empty($file['tmp_name'])) && !empty($_FILES['file']) && is_array($_FILES['file']))
		{
			$file = $_FILES['file'];
		}

		if (!is_array($file) || empty($file['tmp_name']))
		{
			return null;
		}

		return $file;
	}

	private static function uploadErrorMessage($code)
	{
		switch ((int)$code)
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return 'File exceeds upload size limit';
			case UPLOAD_ERR_PARTIAL:
				return 'File was uploaded partially';
			case UPLOAD_ERR_NO_FILE:
				return 'No file received';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing temp folder on server';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'Upload blocked by PHP extension';
			default:
				return 'Upload error (code ' . (int)$code . ')';
		}
	}

	public static function ajaxGetElementTitle($request)
	{
		if (!is_object($request))
		{
			$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		}

		$linkIblockId = (int)$request->getPost('link_iblock_id');
		$elementId = (int)$request->getPost('element_id');
		if ($linkIblockId <= 0 || $elementId <= 0)
		{
			return array(
				'success' => false,
				'error' => 'Invalid parameters',
			);
		}

		$name = self::getElementTitle($linkIblockId, $elementId);

		return array(
			'success' => true,
			'element_id' => $elementId,
			'name' => $name,
		);
	}

	public static function ajaxDelete($request)
	{
		if (!is_object($request))
		{
			$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		}

		$fileId = (int)$request->getPost('file_id');
		if ($fileId > 0)
		{
			CFile::Delete($fileId);
		}

		$elementId = (int)$request->getPost('element_id');
		$iblockId = (int)$request->getPost('iblock_id');
		$propId = (int)$request->getPost('property_id');
		$propCode = trim((string)$request->getPost('property_code'));
		if ($elementId > 0 && $iblockId > 0 && $propId > 0)
		{
			self::writePropertyValue($elementId, $iblockId, $propId, $propCode, false);
		}

		return array('success' => true, 'file_id' => 0);
	}

	private static function decodeHtmlEntitiesDeep($raw)
	{
		$raw = trim((string)$raw);
		if ($raw === '')
		{
			return '';
		}

		for ($i = 0; $i < 6; $i++)
		{
			$next = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
			if ($next === $raw)
			{
				break;
			}
			$raw = $next;
		}

		return $raw;
	}

	private static function escapeForTextarea($raw)
	{
		return str_replace(
			array('&', '<'),
			array('&amp;', '&lt;'),
			(string)$raw
		);
	}

	private static function escapeForHtmlAttribute($raw)
	{
		return htmlspecialchars((string)$raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	private static function restoreValuePayloadFromPostMirror($propId)
	{
		$propId = (int)$propId;
		if ($propId <= 0)
		{
			return null;
		}

		$bags = array();
		if (!empty($_POST['AJAX_IMG_PROP_VALUE']) && is_array($_POST['AJAX_IMG_PROP_VALUE']))
		{
			$bags[] = $_POST['AJAX_IMG_PROP_VALUE'];
		}
		if (!empty($_REQUEST['AJAX_IMG_PROP_VALUE']) && is_array($_REQUEST['AJAX_IMG_PROP_VALUE']))
		{
			$bags[] = $_REQUEST['AJAX_IMG_PROP_VALUE'];
		}

		foreach ($bags as $bag)
		{
			if (!empty($bag[$propId]))
			{
				$value = trim((string)$bag[$propId]);
				if ($value !== '')
				{
					return $value;
				}
			}
		}

		return null;
	}

	private static function findValuePayloadInPropPost($data)
	{
		if (!is_array($data))
		{
			return is_scalar($data) ? trim((string)$data) : null;
		}

		if (array_key_exists('VALUE', $data) && !is_array($data['VALUE']))
		{
			$value = trim((string)$data['VALUE']);
			if ($value !== '')
			{
				return $value;
			}
		}

		foreach ($data as $rowKey => $row)
		{
			if ($rowKey === 'VALUE' || $rowKey === 'DESCRIPTION')
			{
				continue;
			}
			if (!is_array($row))
			{
				continue;
			}
			if (isset($row['VALUE']) && !is_array($row['VALUE']))
			{
				$value = trim((string)$row['VALUE']);
				if ($value !== '')
				{
					return $value;
				}
			}
		}

		return null;
	}

	private static function decodePointsJsonString($raw)
	{
		$raw = self::decodeHtmlEntitiesDeep($raw);
		if ($raw === '')
		{
			return null;
		}

		$decoded = json_decode($raw, true);
		if (is_array($decoded))
		{
			return $decoded;
		}

		$stripped = stripslashes($raw);
		if ($stripped !== $raw)
		{
			$decoded = json_decode($stripped, true);
			if (is_array($decoded))
			{
				return $decoded;
			}
		}

		return null;
	}

	private static function sanitizeSimpleCoord($coord)
	{
		$coord = trim(html_entity_decode((string)$coord, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
		if ($coord === '')
		{
			return '';
		}

		if (preg_match('/^(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)$/u', $coord, $matches))
		{
			return round((float)$matches[1], 2) . ',' . round((float)$matches[2], 2);
		}

		return '';
	}

	private static function isListArray($value)
	{
		if (!is_array($value))
		{
			return false;
		}

		if ($value === array())
		{
			return true;
		}

		return array_keys($value) === range(0, count($value) - 1);
	}

	private static function tryExpandCoordBlob($coord)
	{
		$coord = trim(html_entity_decode((string)$coord, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
		if ($coord === '' || ($coord[0] !== '[' && $coord[0] !== '{'))
		{
			return null;
		}

		$decoded = self::decodePointsJsonString($coord);
		if (!is_array($decoded))
		{
			return null;
		}

		if (isset($decoded['coord']) || isset($decoded['COORD']))
		{
			$decoded = array($decoded);
		}

		if (!self::isListArray($decoded))
		{
			return null;
		}

		$result = array();
		foreach ($decoded as $item)
		{
			$normalized = self::normalizePointItem($item);
			if ($normalized === null)
			{
				continue;
			}

			$nested = self::tryExpandCoordBlob($normalized['coord']);
			if ($nested !== null)
			{
				$result = array_merge($result, $nested);
				continue;
			}

			$normalized['coord'] = self::sanitizeSimpleCoord($normalized['coord']);
			$normalized['elements'] = self::extractElementsFromPoint($normalized);
			if ($normalized['coord'] !== '' || !empty($normalized['elements']))
			{
				$result[] = $normalized;
			}
		}

		return $result;
	}

	private static function normalizeElementItem($item)
	{
		if (!is_array($item))
		{
			return array(
				'element_id' => 0,
				'element_title' => '',
			);
		}

		return array(
			'element_id' => (int)($item['element_id'] ?? $item['ELEMENT_ID'] ?? 0),
			'element_title' => trim((string)($item['element_title'] ?? $item['ELEMENT_TITLE'] ?? '')),
		);
	}

	private static function dedupeElements(array $elements)
	{
		$result = array();
		$seen = array();
		foreach ($elements as $element)
		{
			if (!is_array($element))
			{
				continue;
			}
			$element = self::normalizeElementItem($element);
			$elementId = (int)$element['element_id'];
			if ($elementId <= 0 || isset($seen[$elementId]))
			{
				continue;
			}
			$seen[$elementId] = true;
			$result[] = $element;
		}

		return $result;
	}

	private static function extractElementsFromPoint(array $point)
	{
		$elements = array();
		if (!empty($point['elements']) && is_array($point['elements']))
		{
			foreach ($point['elements'] as $element)
			{
				$normalized = self::normalizeElementItem($element);
				if ((int)$normalized['element_id'] > 0)
				{
					$elements[] = $normalized;
				}
			}
		}

		$legacyId = (int)($point['element_id'] ?? $point['ELEMENT_ID'] ?? 0);
		if ($legacyId > 0)
		{
			$elements[] = array(
				'element_id' => $legacyId,
				'element_title' => trim((string)($point['element_title'] ?? $point['ELEMENT_TITLE'] ?? '')),
			);
		}

		return self::dedupeElements($elements);
	}

	private static function sanitizePointTextField($value, $maxLen)
	{
		$value = trim(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
		if ($value === '')
		{
			return '';
		}

		$maxLen = (int)$maxLen;
		if ($maxLen > 0 && mb_strlen($value) > $maxLen)
		{
			$value = mb_substr($value, 0, $maxLen);
		}

		return $value;
	}

	private static function extractPointMeta(array $point)
	{
		return array(
			'title' => self::sanitizePointTextField(
				$point['title'] ?? $point['TITLE'] ?? $point['name'] ?? $point['NAME'] ?? '',
				self::MAX_POINT_TITLE_LEN
			),
			'factory_number' => self::sanitizePointTextField(
				$point['factory_number'] ?? $point['FACTORY_NUMBER'] ?? $point['serial_number'] ?? $point['SERIAL_NUMBER'] ?? '',
				self::MAX_POINT_FACTORY_NUMBER_LEN
			),
			'quantity' => self::sanitizePointTextField(
				$point['quantity'] ?? $point['QUANTITY'] ?? $point['qty'] ?? $point['QTY'] ?? '',
				self::MAX_POINT_QUANTITY_LEN
			),
		);
	}

	private static function mergePointMeta(array $existing, array $incoming)
	{
		foreach (array('title', 'factory_number', 'quantity') as $key)
		{
			if (($existing[$key] ?? '') === '' && ($incoming[$key] ?? '') !== '')
			{
				$existing[$key] = $incoming[$key];
			}
		}

		return $existing;
	}

	private static function pointHasContent(array $point)
	{
		if (self::sanitizeSimpleCoord((string)($point['coord'] ?? '')) !== '')
		{
			return true;
		}

		$meta = self::extractPointMeta($point);
		if ($meta['title'] !== '' || $meta['factory_number'] !== '' || $meta['quantity'] !== '')
		{
			return true;
		}

		return !empty(self::extractElementsFromPoint($point));
	}

	private static function mergePointsByCoord(array $points)
	{
		$map = array();
		foreach ($points as $point)
		{
			if (!is_array($point))
			{
				continue;
			}

			$coord = self::sanitizeSimpleCoord((string)($point['coord'] ?? ''));
			$meta = self::extractPointMeta($point);
			$elements = self::extractElementsFromPoint($point);
			if (!self::pointHasContent($point))
			{
				continue;
			}

			$key = $coord !== '' ? $coord : '__row_' . count($map);
			if (!isset($map[$key]))
			{
				$map[$key] = array(
					'coord' => $coord,
					'title' => '',
					'factory_number' => '',
					'quantity' => '',
					'elements' => array(),
				);
			}

			$map[$key] = self::mergePointMeta($map[$key], $meta);
			$map[$key]['elements'] = self::dedupeElements(array_merge($map[$key]['elements'], $elements));
		}

		$result = array_values($map);
		if (count($result) > self::MAX_POINTS)
		{
			$result = array_slice($result, 0, self::MAX_POINTS);
		}

		foreach ($result as &$point)
		{
			if (count($point['elements']) > self::MAX_ELEMENTS_PER_POINT)
			{
				$point['elements'] = array_slice($point['elements'], 0, self::MAX_ELEMENTS_PER_POINT);
			}
		}
		unset($point);

		return $result;
	}

	private static function flattenPointsList(array $points)
	{
		$items = array();
		foreach ($points as $point)
		{
			if (!is_array($point))
			{
				continue;
			}

			$coord = (string)($point['coord'] ?? '');
			$expanded = self::tryExpandCoordBlob($coord);
			if ($expanded !== null)
			{
				$items = array_merge($items, $expanded);
				continue;
			}

			$normalized = self::normalizePointItem($point);
			if ($normalized !== null)
			{
				$items[] = $normalized;
			}
		}

		return self::mergePointsByCoord($items);
	}

	private static function normalizePointItem($item)
	{
		if (is_string($item))
		{
			$expanded = self::tryExpandCoordBlob($item);
			if ($expanded !== null && !empty($expanded))
			{
				return $expanded[0];
			}

			$normalized = array(
				'coord' => self::sanitizeSimpleCoord($item),
				'elements' => array(),
			);

			return array_merge($normalized, self::extractPointMeta($normalized));
		}

		if (!is_array($item))
		{
			return null;
		}

		$coord = self::sanitizeSimpleCoord($item['coord'] ?? $item['COORD'] ?? '');
		$normalized = array(
			'coord' => $coord,
			'elements' => self::extractElementsFromPoint($item),
		);

		return array_merge($normalized, self::extractPointMeta($item));
	}

	private static function parsePointsList($description)
	{
		$description = trim((string)$description);
		if ($description === '')
		{
			return array();
		}

		$decoded = self::decodePointsJsonString($description);
		if (is_array($decoded))
		{
			if (isset($decoded['coord']) || isset($decoded['COORD']))
			{
				$decoded = array($decoded);
			}

			$result = array();
			if (self::isListArray($decoded))
			{
				foreach ($decoded as $item)
				{
					$normalized = self::normalizePointItem($item);
					if ($normalized === null)
					{
						continue;
					}
					if (self::pointHasContent($normalized))
					{
						$result[] = $normalized;
					}
				}
			}

			return self::flattenPointsList($result);
		}

		$lines = preg_split('/\R+/u', $description);
		if (!is_array($lines))
		{
			return array();
		}

		$result = array();
		foreach ($lines as $line)
		{
			$line = trim((string)$line);
			if ($line !== '')
			{
				$result[] = array(
					'coord' => $line,
					'elements' => array(),
				);
			}
		}

		return self::flattenPointsList($result);
	}

	private static function getElementTitle($iblockId, $elementId)
	{
		$iblockId = (int)$iblockId;
		$elementId = (int)$elementId;
		if ($iblockId <= 0 || $elementId <= 0)
		{
			return '';
		}

		if (!\Bitrix\Main\Loader::includeModule('iblock'))
		{
			return '';
		}

		$rs = CIBlockElement::GetList(
			array(),
			array('IBLOCK_ID' => $iblockId, 'ID' => $elementId),
			false,
			array('nTopCount' => 1),
			array('ID', 'NAME')
		);
		if ($row = $rs->Fetch())
		{
			return trim((string)($row['NAME'] ?? ''));
		}

		return '';
	}

	private static function enrichPointsList(array $points, $linkIblockId)
	{
		$linkIblockId = (int)$linkIblockId;
		foreach ($points as &$point)
		{
			if (!is_array($point))
			{
				continue;
			}
			$point['elements'] = self::extractElementsFromPoint($point);
			foreach ($point['elements'] as &$element)
			{
				$elementId = (int)($element['element_id'] ?? 0);
				if ($elementId > 0 && ($element['element_title'] ?? '') === '')
				{
					$element['element_title'] = self::getElementTitle($linkIblockId, $elementId);
				}
			}
			unset($element);
		}
		unset($point);

		return $points;
	}

	private static function encodePointsList(array $points)
	{
		$result = array();
		foreach (self::mergePointsByCoord($points) as $point)
		{
			if (!self::pointHasContent($point))
			{
				continue;
			}
			$coord = self::sanitizeSimpleCoord((string)($point['coord'] ?? ''));
			$meta = self::extractPointMeta($point);
			$elements = self::extractElementsFromPoint($point);
			$entry = array(
				'coord' => $coord,
				'elements' => $elements,
			);
			foreach ($meta as $metaKey => $metaValue)
			{
				if ($metaValue !== '')
				{
					$entry[$metaKey] = $metaValue;
				}
			}
			$result[] = $entry;
		}

		return json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	private static function parseValuePayload($raw, $linkIblockId = 0)
	{
		$fileId = 0;
		$points = array();

		if (is_array($raw))
		{
			if (isset($raw['file_id']) || isset($raw['points']))
			{
				$fileId = (int)($raw['file_id'] ?? 0);
				$pointsRaw = $raw['points'] ?? array();
				if (is_string($pointsRaw))
				{
					$points = self::parsePointsList($pointsRaw);
				}
				elseif (is_array($pointsRaw))
				{
					$points = self::flattenPointsList($pointsRaw);
				}
			}
			elseif (isset($raw['VALUE']) || isset($raw['DESCRIPTION']))
			{
				return self::unpackPropertyValue($raw, $linkIblockId);
			}
			else
			{
				$points = self::flattenPointsList($raw);
			}

			if ($linkIblockId > 0 && !empty($points))
			{
				$points = self::enrichPointsList($points, $linkIblockId);
			}

			return array(
				'file_id' => $fileId,
				'points' => $points,
			);
		}

		$str = self::decodeHtmlEntitiesDeep($raw);
		if ($str === '')
		{
			return array(
				'file_id' => 0,
				'points' => array(),
			);
		}

		$decoded = self::decodePointsJsonString($str);
		if (is_array($decoded))
		{
			if (isset($decoded['file_id']) || isset($decoded['points']))
			{
				$fileId = (int)($decoded['file_id'] ?? 0);
				$pointsRaw = $decoded['points'] ?? array();
				if (is_string($pointsRaw))
				{
					$points = self::parsePointsList($pointsRaw);
				}
				elseif (is_array($pointsRaw))
				{
					$points = self::flattenPointsList($pointsRaw);
				}
			}
			elseif (self::isListArray($decoded))
			{
				$points = self::parsePointsList($str);
				$fileId = 0;
			}
		}
		elseif (preg_match('/^\d+$/', $str))
		{
			$fileId = (int)$str;
		}

		if ($linkIblockId > 0 && !empty($points))
		{
			$points = self::enrichPointsList($points, $linkIblockId);
		}

		return array(
			'file_id' => $fileId,
			'points' => $points,
		);
	}

	private static function unpackPropertyValue($arValue, $linkIblockId = 0)
	{
		$linkIblockId = (int)$linkIblockId;
		$valueRaw = '';
		$descRaw = '';

		if (is_array($arValue))
		{
			$valueRaw = $arValue['~VALUE'] ?? $arValue['VALUE'] ?? '';
			$descRaw = $arValue['~DESCRIPTION'] ?? $arValue['DESCRIPTION'] ?? '';
		}
		else
		{
			$valueRaw = $arValue;
		}

		$payload = self::parseValuePayload($valueRaw, $linkIblockId);
		$fileId = (int)$payload['file_id'];
		$points = $payload['points'];

		if (empty($points) && trim((string)$descRaw) !== '')
		{
			$points = self::parsePointsList($descRaw);
			if ($linkIblockId > 0)
			{
				$points = self::enrichPointsList($points, $linkIblockId);
			}
		}

		if ($fileId <= 0)
		{
			$strValue = trim((string)$valueRaw);
			if ($strValue !== '' && preg_match('/^\d+$/', $strValue))
			{
				$fileId = (int)$strValue;
			}
		}

		return array(
			'file_id' => $fileId,
			'points' => $points,
		);
	}

	private static function encodeValuePayload($fileId, array $points, $linkIblockId = 0)
	{
		$fileId = (int)$fileId;
		$linkIblockId = (int)$linkIblockId;

		if ($linkIblockId > 0)
		{
			$points = self::enrichPointsList($points, $linkIblockId);
		}

		$points = self::flattenPointsList($points);
		$normalizedJson = self::encodePointsList($points);
		$points = json_decode($normalizedJson, true);
		if (!is_array($points))
		{
			$points = array();
		}

		return json_encode(
			array(
				'file_id' => $fileId,
				'points' => $points,
			),
			JSON_UNESCAPED_UNICODE
		);
	}

	private static function mergeValuePayloads(array $base, array $add)
	{
		if ((int)($add['file_id'] ?? 0) > 0)
		{
			$base['file_id'] = (int)$add['file_id'];
		}

		if (!empty($add['points']) && is_array($add['points']))
		{
			$base['points'] = $add['points'];
		}

		return $base;
	}

	private static function resolveValuePayloadFromRequest($arProperty, $arValue)
	{
		$linkIblockId = self::getLinkIblockId($arProperty);
		$payload = array(
			'file_id' => 0,
			'points' => array(),
		);

		$fromRequest = self::restoreValuePayloadFromRequest($arProperty);
		if ($fromRequest !== null && $fromRequest !== '')
		{
			$payload = self::mergeValuePayloads($payload, self::parseValuePayload($fromRequest, $linkIblockId));
		}

		if (is_array($arValue))
		{
			$payload = self::mergeValuePayloads($payload, self::unpackPropertyValue($arValue, $linkIblockId));
		}

		if ($linkIblockId > 0 && !empty($payload['points']))
		{
			$payload['points'] = self::enrichPointsList($payload['points'], $linkIblockId);
		}

		return $payload;
	}

	private static function restoreValuePayloadFromRequest($arProperty)
	{
		$propId = (int)($arProperty['ID'] ?? 0);
		if ($propId <= 0)
		{
			return null;
		}

		$linkIblockId = self::getLinkIblockId($arProperty);

		$mirrorRaw = self::restoreValuePayloadFromPostMirror($propId);
		if ($mirrorRaw !== null && $mirrorRaw !== '')
		{
			return $mirrorRaw;
		}

		foreach (self::getPropPostBuckets($propId, (string)($arProperty['CODE'] ?? '')) as $data)
		{
			$valueRaw = self::findValuePayloadInPropPost($data);
			if ($valueRaw !== null && $valueRaw !== '')
			{
				return $valueRaw;
			}

			if (!is_array($data))
			{
				continue;
			}

			// Legacy: points were stored in DESCRIPTION
			foreach ($data as $rowKey => $row)
			{
				if (!is_array($row))
				{
					continue;
				}
				$desc = trim((string)($row['DESCRIPTION'] ?? ''));
				if ($desc !== '')
				{
					$fileId = self::extractFileIdFromPropPost($data);
					return self::encodeValuePayload($fileId, self::parsePointsList($desc), $linkIblockId);
				}
			}
			if (array_key_exists('DESCRIPTION', $data))
			{
				$desc = trim((string)$data['DESCRIPTION']);
				if ($desc !== '')
				{
					$fileId = self::extractFileIdFromPropPost($data);
					return self::encodeValuePayload($fileId, self::parsePointsList($desc), $linkIblockId);
				}
			}
		}

		return null;
	}

	private static function getStoredValuePayloadForElement($iblockId, $elementId, $propId)
	{
		$iblockId = (int)$iblockId;
		$elementId = (int)$elementId;
		$propId = (int)$propId;

		if ($iblockId <= 0 || $elementId <= 0 || $propId <= 0)
		{
			return array(
				'file_id' => 0,
				'points' => array(),
			);
		}

		if (!\Bitrix\Main\Loader::includeModule('iblock'))
		{
			return array(
				'file_id' => 0,
				'points' => array(),
			);
		}

		$propRow = CIBlockProperty::GetByID($propId)->Fetch();
		$linkIblockId = is_array($propRow) ? self::getLinkIblockId($propRow) : 0;

		$rs = CIBlockElement::GetProperty($iblockId, $elementId, array('sort' => 'asc'), array('ID' => $propId));
		while ($row = $rs->Fetch())
		{
			return self::unpackPropertyValue($row, $linkIblockId);
		}

		return array(
			'file_id' => 0,
			'points' => array(),
		);
	}

	private static function getPickIconHtml()
	{
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">'
			. '<circle cx="8" cy="8" r="2.5" fill="none" stroke="currentColor" stroke-width="1.25"/>'
			. '<path d="M8 1.5v2.75M8 11.75v2.75M1.5 8h2.75M11.75 8h2.75" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>'
			. '</svg>';
	}

	private static function getAddElementIconHtml()
	{
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">'
			. '<path d="M5.5 6.5a3 3 0 0 1 4.24 0l1.26 1.26a3 3 0 0 1-4.24 4.24L6 11.5" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>'
			. '<path d="M10.5 9.5a3 3 0 0 1-4.24 0L5 8.24a3 3 0 0 1 4.24-4.24L10 4.5" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>'
			. '<path d="M11.5 11.5v2.25M10.375 12.625h2.25" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>'
			. '</svg>';
	}

	private static function getPointActionBtnStyle()
	{
		return 'min-width:28px;width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;line-height:1;';
	}

	private static function renderPointFieldsHtml(array $point)
	{
		$meta = self::extractPointMeta($point);

		$html = '<div class="ajax-img-point-fields">';
		$html .= '<div class="ajax-img-point-field">';
		$html .= '<span class="ajax-img-point-field-label">'
			. "\xD0\x9D\xD0\xB0\xD0\xB7\xD0\xB2\xD0\xB0\xD0\xBD\xD0\xB8\xD0\xB5"
			. '</span>';
		$html .= '<input type="text" class="ajax-img-point-title adm-input" maxlength="'
			. self::MAX_POINT_TITLE_LEN
			. '" value="' . htmlspecialcharsbx($meta['title']) . '">';
		$html .= '</div>';
		$html .= '<div class="ajax-img-point-field">';
		$html .= '<span class="ajax-img-point-field-label">'
			. "\xD0\x97\xD0\xB0\xD0\xB2\xD0\xBE\xD0\xB4\xD1\x81\xD0\xBA\xD0\xBE\xD0\xB9 \xD0\xBD\xD0\xBE\xD0\xBC\xD0\xB5\xD1\x80"
			. '</span>';
		$html .= '<input type="text" class="ajax-img-point-factory-number adm-input" maxlength="'
			. self::MAX_POINT_FACTORY_NUMBER_LEN
			. '" value="' . htmlspecialcharsbx($meta['factory_number']) . '">';
		$html .= '</div>';
		$html .= '<div class="ajax-img-point-field">';
		$html .= '<span class="ajax-img-point-field-label">'
			. "\xD0\x9A\xD0\xBE\xD0\xBB\xD0\xB8\xD1\x87\xD0\xB5\xD1\x81\xD1\x82\xD0\xB2\xD0\xBE"
			. '</span>';
		$html .= '<input type="text" class="ajax-img-point-quantity adm-input" maxlength="'
			. self::MAX_POINT_QUANTITY_LEN
			. '" value="' . htmlspecialcharsbx($meta['quantity']) . '">';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	private static function renderPointElementsBoxHtml(array $elements)
	{
		$elements = self::dedupeElements(is_array($elements) ? $elements : array());
		$elementsJson = json_encode($elements, JSON_UNESCAPED_UNICODE);
		if (!is_string($elementsJson))
		{
			$elementsJson = '[]';
		}

		$html = '<div class="ajax-img-point-elements">';
		if (empty($elements))
		{
			$html .= '<span class="ajax-img-point-elements-empty">'
				. "\xE2\x80\x94"
				. '</span>';
		}
		else
		{
			foreach ($elements as $element)
			{
				$elementId = (int)($element['element_id'] ?? 0);
				if ($elementId <= 0)
				{
					continue;
				}
				$elementTitle = trim((string)($element['element_title'] ?? ''));
				if ($elementTitle === '')
				{
					$elementTitle = "\xD0\xAD\xD0\xBB\xD0\xB5\xD0\xBC\xD0\xB5\xD0\xBD\xD1\x82 #{$elementId}";
				}
				$html .= '<span class="ajax-img-point-element-chip" data-element-id="' . $elementId . '">';
				$html .= '<span class="ajax-img-point-element-chip-name">' . htmlspecialcharsbx($elementTitle) . '</span>';
				$html .= '<button type="button" class="ajax-img-point-element-chip-remove" title="'
					. "\xD0\xA3\xD0\xB4\xD0\xB0\xD0\xBB\xD0\xB8\xD1\x82\xD1\x8C"
					. '">&times;</button>';
				$html .= '</span>';
			}
		}
		$html .= '<input type="hidden" class="ajax-img-point-elements-json" value="'
			. self::escapeForHtmlAttribute($elementsJson)
			. '">';
		$html .= '</div>';

		return $html;
	}

	private static function renderPointRowHtml(array $point, $index)
	{
		$coord = trim((string)($point['coord'] ?? ''));
		$elements = self::extractElementsFromPoint($point);
		$coordClass = $coord !== '' ? 'ajax-img-point-coord-text' : 'ajax-img-point-coord-text is-empty';
		$coordText = $coord !== '' ? $coord : "\xE2\x80\x94";

		$html = '<div class="ajax-img-point-row"'
			. ' data-point-coord="' . htmlspecialcharsbx($coord) . '">';
		$html .= '<span class="ajax-img-point-index">' . (int)$index . '</span>';
		$html .= '<div class="ajax-img-point-body">';
		$html .= self::renderPointFieldsHtml($point);
		$html .= self::renderPointElementsBoxHtml($elements);
		$html .= '</div>';
		$html .= '<span class="' . $coordClass . '">' . htmlspecialcharsbx($coordText) . '</span>';
		$html .= '<div class="ajax-img-point-actions">';
		$html .= '<button type="button" class="adm-btn ajax-img-point-pick" title="'
			. "\xD0\x9D\xD0\xB0 \xD0\xBA\xD0\xB0\xD1\x80\xD1\x82\xD0\xB8\xD0\xBD\xD0\xBA\xD0\xB5"
			. '" aria-label="'
			. "\xD0\x9D\xD0\xB0 \xD0\xBA\xD0\xB0\xD1\x80\xD1\x82\xD0\xB8\xD0\xBD\xD0\xBA\xD0\xB5"
			. '" style="' . self::getPointActionBtnStyle() . '">'
			. self::getPickIconHtml() . '</button>';
		$html .= '<button type="button" class="adm-btn ajax-img-point-element-pick" title="'
			. "\xD0\x94\xD0\xBE\xD0\xB1\xD0\xB0\xD0\xB2\xD0\xB8\xD1\x82\xD1\x8C \xD1\x8D\xD0\xBB\xD0\xB5\xD0\xBC\xD0\xB5\xD0\xBD\xD1\x82"
			. '" aria-label="'
			. "\xD0\x94\xD0\xBE\xD0\xB1\xD0\xB0\xD0\xB2\xD0\xB8\xD1\x82\xD1\x8C \xD1\x8D\xD0\xBB\xD0\xB5\xD0\xBC\xD0\xB5\xD0\xBD\xD1\x82"
			. '" style="' . self::getPointActionBtnStyle() . '">'
			. self::getAddElementIconHtml() . '</button>';
		$html .= '<button type="button" class="adm-btn ajax-img-point-remove" title="'
			. "\xD0\xA3\xD0\xB4\xD0\xB0\xD0\xBB\xD0\xB8\xD1\x82\xD1\x8C \xD1\x82\xD0\xBE\xD1\x87\xD0\xBA\xD1\x83"
			. '">&times;</button>';
		$html .= '</div>';
		$html .= '<input type="hidden" class="ajax-img-point-input" value="' . htmlspecialcharsbx($coord) . '">';
		$html .= '</div>';

		return $html;
	}

	private static function renderPointsListHtml(array $points)
	{
		if (empty($points))
		{
			$points = array(
				array(
					'coord' => '',
					'title' => '',
					'factory_number' => '',
					'quantity' => '',
					'elements' => array(),
				),
			);
		}

		$html = '<div class="ajax-img-points-head">';
		$html .= '<span class="ajax-img-points-head-num">'
			. "\xE2\x84\x96"
			. '</span>';
		$html .= '<span class="ajax-img-points-head-el">'
			. "\xD0\x94\xD0\xB0\xD0\xBD\xD0\xBD\xD1\x8B\xD0\xB5 \xD0\xBC\xD0\xB5\xD1\x82\xD0\xBA\xD0\xB8"
			. '</span>';
		$html .= '<span class="ajax-img-points-head-coord">'
			. "\xD0\x9A\xD0\xBE\xD0\xBE\xD1\x80\xD0\xB4\xD0\xB8\xD0\xBD\xD0\xB0\xD1\x82\xD1\x8B"
			. '</span>';
		$html .= '<span class="ajax-img-points-head-act"></span>';
		$html .= '</div>';

		$index = 0;
		foreach ($points as $point)
		{
			if (!is_array($point))
			{
				continue;
			}
			$index++;
			$html .= self::renderPointRowHtml($point, $index);
		}

		if ($index === 0)
		{
			$html .= self::renderPointRowHtml(array(
				'coord' => '',
				'title' => '',
				'factory_number' => '',
				'quantity' => '',
				'elements' => array(),
			), 1);
		}

		return $html;
	}

	private static function renderBlock($arProperty, $value, $strHTMLControlName)
	{
		$propId = (int)($arProperty['ID'] ?? 0);
		$iblockId = (int)($arProperty['IBLOCK_ID'] ?? 0);
		$propCode = (string)($arProperty['CODE'] ?? '');
		$linkIblockId = self::getLinkIblockId($arProperty);
		try
		{
			$payload = self::unpackPropertyValue($value, $linkIblockId);
		}
		catch (\Throwable $e)
		{
			$payload = array(
				'file_id' => 0,
				'points' => array(),
			);
		}
		$payload['points'] = self::flattenPointsList(is_array($payload['points']) ? $payload['points'] : array());
		$fileId = (int)$payload['file_id'];
		$name = (string)$strHTMLControlName['VALUE'];
		$nameAttr = htmlspecialcharsbx($name);
		$valueJson = self::encodeValuePayload($fileId, $payload['points'], $linkIblockId);
		$uid = 'ajax-img-' . substr(md5($name . '|' . $propId), 0, 10);
		$preview = self::getPreviewHtml($fileId);

		$valueAttr = self::escapeForHtmlAttribute($valueJson);
		$postMirrorName = 'AJAX_IMG_PROP_VALUE[' . $propId . ']';
		$html = '<div class="ajax-img-prop" id="' . htmlspecialcharsbx($uid) . '" data-ajax-url="/local/ajax/ajax_image_property.php"'
			. ' data-prop-id="' . $propId . '" data-iblock-id="' . $iblockId . '" data-link-iblock-id="' . $linkIblockId . '"'
			. ' data-prop-code="' . htmlspecialcharsbx($propCode) . '"'
			. ' data-value-field-name="' . $nameAttr . '"'
			. ' data-post-mirror-name="' . htmlspecialcharsbx($postMirrorName) . '">';
		$html .= '<input type="hidden" class="ajax-img-value-json" name="' . $nameAttr . '" value="' . $valueAttr . '">';
		$html .= '<input type="hidden" class="ajax-img-value-post" name="' . htmlspecialcharsbx($postMirrorName) . '" value="' . $valueAttr . '">';
		$html .= '<textarea class="ajax-img-value-debug" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" readonly="readonly" tabindex="-1" aria-hidden="true">'
			. self::escapeForTextarea($valueJson)
			. '</textarea>';
		$html .= '<input type="hidden" class="ajax-img-id" value="' . $fileId . '">';
		$html .= '<input type="hidden" class="ajax-img-del" name="' . $nameAttr . '_del" value="">';

		$html .= '<div class="ajax-img-preview" style="margin-bottom:8px;' . self::getPreviewBoxCss() . ($preview === '' ? 'display:none;' : '') . '">';
		$html .= '<div class="ajax-img-markers"></div>';
		$html .= $preview;
		$html .= '<div class="ajax-img-pick-hint" style="display:none;position:absolute;left:8px;top:8px;background:rgba(0,0,0,.65);color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;z-index:2;pointer-events:none;">'
			. "\xD0\x9A\xD0\xBB\xD0\xB8\xD0\xBA\xD0\xBD\xD0\xB8\xD1\x82\xD0\xB5 \xD0\xBF\xD0\xBE \xD0\xB8\xD0\xB7\xD0\xBE\xD0\xB1\xD1\x80\xD0\xB0\xD0\xB6\xD0\xB5\xD0\xBD\xD0\xB8\xD1\x8E"
			. '</div>';
		$html .= '</div>';
		$html .= '<div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">';
		$html .= '<input type="file" class="ajax-img-input" accept="image/*" style="max-width:280px;">';
		$html .= '<button type="button" class="adm-btn ajax-img-delete"' . ($fileId > 0 ? '' : ' style="display:none;"') . '>' . "\xD0\xA3\xD0\xB4\xD0\xB0\xD0\xBB\xD0\xB8\xD1\x82\xD1\x8C" . '</button>';
		$html .= '<span class="ajax-img-status" style="color:#6a737d;font-size:12px;"></span>';
		$html .= '</div>';

		$html .= '<div class="ajax-img-points" style="display:block;width:100%;margin-top:12px;">';
		$html .= '<div style="font-weight:600;margin-bottom:6px;">'
			. "\xD0\x9A\xD0\xBE\xD0\xBE\xD1\x80\xD0\xB4\xD0\xB8\xD0\xBD\xD0\xB0\xD1\x82\xD1\x8B \xD0\xBD\xD0\xB0 \xD0\xB8\xD0\xB7\xD0\xBE\xD0\xB1\xD1\x80\xD0\xB0\xD0\xB6\xD0\xB5\xD0\xBD\xD0\xB8\xD0\xB8 (\x25, \xD1\x84\xD0\xBE\xD1\x80\xD0\xBC\xD0\xB0\xD1\x82 X,Y)"
			. '</div>';
		$html .= '<div class="ajax-img-points-list" data-ssr="1">';
		$html .= self::renderPointsListHtml($payload['points']);
		$html .= '</div>';
		$html .= '<button type="button" class="adm-btn ajax-img-points-add" style="margin-top:6px;">'
			. "\xD0\x94\xD0\xBE\xD0\xB1\xD0\xB0\xD0\xB2\xD0\xB8\xD1\x82\xD1\x8C \xD0\xBF\xD0\xBE\xD0\xBB\xD0\xB5"
			. '</button>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= self::renderScriptOnce();
		$escapedUid = htmlspecialcharsbx($uid);
		$html .= '<script>(function(){var id="' . $escapedUid . '";function boot(){var el=document.getElementById(id);'
			. 'if(!el){return;}if(window.ajaxImgBindProperty){window.ajaxImgBindProperty(el);return;}'
			. 'setTimeout(boot,40);}if(window.BX&&BX.ready){BX.ready(boot);}'
			. 'else if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",boot);}'
			. 'else{boot();}})();</script>';

		return $html;
	}

	private static function getPreviewCss()
	{
		return 'display:block;max-width:100%;max-height:75vh;width:auto;height:auto;'
			. 'border:1px solid #c6cdd3;border-radius:4px;background:#fff;vertical-align:top;';
	}

	private static function getPreviewBoxCss()
	{
		return 'display:inline-block;max-width:100%;position:relative;vertical-align:top;line-height:0;';
	}

	private static function getPreviewSrc($fileId)
	{
		$fileId = (int)$fileId;
		if ($fileId <= 0)
		{
			return '';
		}

		$maxSide = (int)self::PREVIEW_MAX_SIDE;
		if ($maxSide <= 0)
		{
			return (string)CFile::GetPath($fileId);
		}

		$arFile = CFile::GetFileArray($fileId);
		if (!is_array($arFile))
		{
			return (string)CFile::GetPath($fileId);
		}

		$width = (int)($arFile['WIDTH'] ?? 0);
		$height = (int)($arFile['HEIGHT'] ?? 0);
		if ($width <= $maxSide && $height <= $maxSide)
		{
			return (string)CFile::GetPath($fileId);
		}

		$resize = CFile::ResizeImageGet(
			$fileId,
			array('width' => $maxSide, 'height' => $maxSide),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);

		if (is_array($resize) && !empty($resize['src']))
		{
			return (string)$resize['src'];
		}

		return (string)CFile::GetPath($fileId);
	}

	private static function getPreviewHtml($fileId)
	{
		$fileId = (int)$fileId;
		if ($fileId <= 0)
		{
			return '';
		}

		$src = self::getPreviewSrc($fileId);
		if ($src === '')
		{
			return '';
		}

		return '<img src="' . htmlspecialcharsbx($src) . '" alt="" class="ajax-img-preview-img" style="' . self::getPreviewCss() . '">';
	}

	private static function getPointsInlineCssText()
	{
		return '.ajax-img-preview{display:inline-block!important;max-width:100%!important;position:relative;line-height:0;}'
			. '.ajax-img-preview-img{display:block;max-width:100%;max-height:75vh;width:auto;height:auto;}'
			. '.ajax-img-markers{box-sizing:border-box;}'
			. '.ajax-img-points{display:block!important;width:100%;margin-top:12px;}'
			. '.ajax-img-points-list{display:flex!important;flex-direction:column;gap:6px;margin-top:8px;width:100%;min-height:48px;}'
			. '.ajax-img-points-head,.ajax-img-point-row{display:grid!important;'
			. 'grid-template-columns:28px minmax(260px,1fr) 110px 132px;align-items:start;column-gap:10px;width:100%;box-sizing:border-box;}'
			. '.ajax-img-points-head{padding:0 8px 4px;font-size:11px;font-weight:600;color:#6a737d;text-transform:uppercase;}'
			. '.ajax-img-point-row{padding:8px;background:#f8f9fa;border:1px solid #e6e8eb;border-radius:4px;}'
			. '.ajax-img-point-index{font-size:12px;font-weight:600;color:#6a737d;text-align:center;padding-top:6px;}'
			. '.ajax-img-point-body{display:flex;flex-direction:column;gap:6px;min-width:0;}'
			. '.ajax-img-point-fields{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px;}'
			. '.ajax-img-point-field{display:flex;flex-direction:column;gap:2px;min-width:0;}'
			. '.ajax-img-point-field-label{font-size:10px;font-weight:600;color:#6a737d;text-transform:uppercase;letter-spacing:.02em;}'
			. '.ajax-img-point-field input{width:100%;box-sizing:border-box;font-size:12px;padding:4px 6px;}'
			. '.ajax-img-point-elements{display:flex;flex-wrap:wrap;gap:4px;align-items:center;min-width:0;}'
			. '.ajax-img-point-elements-empty{color:#9aa0a6;font-size:12px;}'
			. '.ajax-img-point-element-chip{display:inline-flex;align-items:center;gap:4px;max-width:100%;padding:2px 6px;background:#fff;border:1px solid #d0d7de;border-radius:4px;font-size:11px;}'
			. '.ajax-img-point-element-chip-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;}'
			. '.ajax-img-point-element-chip-remove{border:none;background:transparent;cursor:pointer;color:#888;padding:0 2px;line-height:1;}'
			. '.ajax-img-point-coord-text{font-size:12px;color:#1a1a1a;}'
			. '.ajax-img-point-actions{display:flex;align-items:center;justify-content:flex-end;gap:4px;}'
			. '.ajax-img-point-pick,.ajax-img-point-element-pick{min-width:28px;width:28px;height:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;line-height:1;}';
	}

	private static function getAdminLayoutCssText()
	{
		$path = $_SERVER['DOCUMENT_ROOT'] . '/local/css/ajax_image_property_admin.css';
		if (is_file($path) && is_readable($path))
		{
			$css = (string)file_get_contents($path);
			if ($css !== '' && strpos($css, '.ajax-img-points') !== false)
			{
				return $css;
			}
		}

		return 'table.adm-detail-content-table{table-layout:fixed!important;width:100%!important;}'
			. 'table.adm-detail-content-table td.adm-detail-content-cell-l,'
			. 'td.adm-detail-content-cell-l{width:15%!important;}'
			. 'table.adm-detail-content-table td.adm-detail-content-cell-r,'
			. 'td.adm-detail-content-cell-r{width:85%!important;}'
			. self::getPointsInlineCssText();
	}

	private static function renderAdminLayoutCssOnce()
	{
		static $done = false;
		if ($done)
		{
			return '';
		}
		$done = true;

		return '<style id="ajax-img-admin-layout-css" type="text/css">'
			. self::getAdminLayoutCssText()
			. '</style>'
			. '<style id="ajax-img-points-inline-css" type="text/css">'
			. self::getPointsInlineCssText()
			. '</style>';
	}

	private static function registerAdminAssets()
	{
		if (!class_exists('\Bitrix\Main\Page\Asset'))
		{
			return;
		}

		$asset = \Bitrix\Main\Page\Asset::getInstance();
		$jsPath = $_SERVER['DOCUMENT_ROOT'] . '/local/js/ajax_image_property.js';
		$jsUrl = '/local/js/ajax_image_property.js';
		if (is_file($jsPath))
		{
			$jsUrl .= '?v=' . (int)filemtime($jsPath);
		}
		$asset->addJs($jsUrl);
		$cssPath = $_SERVER['DOCUMENT_ROOT'] . '/local/css/ajax_image_property_admin.css';
		$cssUrl = '/local/css/ajax_image_property_admin.css';
		if (is_file($cssPath))
		{
			$cssUrl .= '?v=' . (int)filemtime($cssPath);
		}
		$asset->addCss($cssUrl);
	}

	public static function onBeforePrologAdminStyles()
	{
		if (!defined('ADMIN_SECTION') || ADMIN_SECTION !== true)
		{
			return;
		}

		$script = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
		$allowed = array(
			'iblock_element_edit.php',
			'iblock_element_admin.php',
			'iblock_section_edit.php',
			'cat_product_edit.php',
		);
		$isElementEdit = in_array($script, $allowed, true)
			|| (strpos($script, 'iblock') !== false && strpos($script, 'element') !== false);

		if (!$isElementEdit && empty($_GET['IBLOCK_ID']) && empty($_POST['IBLOCK_ID']))
		{
			return;
		}

		self::registerAdminAssets();
	}

	private static function renderScriptOnce()
	{
		static $done = false;
		if ($done)
		{
			return '';
		}
		$done = true;

		self::registerAdminAssets();

		$html = self::renderAdminLayoutCssOnce();
		if (!class_exists('\Bitrix\Main\Page\Asset'))
		{
			$jsPath = $_SERVER['DOCUMENT_ROOT'] . '/local/js/ajax_image_property.js';
			$jsUrl = '/local/js/ajax_image_property.js';
			if (is_file($jsPath))
			{
				$jsUrl .= '?v=' . (int)filemtime($jsPath);
			}
			$html .= '<script src="' . htmlspecialcharsbx($jsUrl) . '"></script>';
		}

		return $html;
	}

	private static function isDeleteFlagged($arProperty)
	{
		$propId = (int)($arProperty['ID'] ?? 0);
		$propCode = (string)($arProperty['CODE'] ?? '');

		foreach (self::getPropPostBuckets($propId, $propCode) as $data)
		{
			if (!is_array($data))
			{
				continue;
			}
			if (isset($data['VALUE_del']) && (string)$data['VALUE_del'] === 'Y')
			{
				return true;
			}
			foreach ($data as $row)
			{
				if (is_array($row) && isset($row['VALUE_del']) && (string)$row['VALUE_del'] === 'Y')
				{
					return true;
				}
			}
		}

		return false;
	}

	private static function getPropPostBuckets($propId, $propCode)
	{
		$buckets = array();
		if ($propId > 0 && isset($_POST['PROP'][$propId]))
		{
			$buckets[] = $_POST['PROP'][$propId];
		}
		if ($propCode !== '' && isset($_POST['PROP'][$propCode]))
		{
			$buckets[] = $_POST['PROP'][$propCode];
		}

		return $buckets;
	}

	public static function collectRawFileIdsFromPropPost($iblockId)
	{
		$result = array();
		$iblockId = (int)$iblockId;
		$propData = array();

		if (!empty($_POST['PROP']) && is_array($_POST['PROP']))
		{
			$propData = $_POST['PROP'];
		}
		elseif (!empty($_REQUEST['PROP']) && is_array($_REQUEST['PROP']))
		{
			$propData = $_REQUEST['PROP'];
		}

		if (empty($propData))
		{
			return $result;
		}

		foreach ($propData as $key => $data)
		{
			$propId = self::resolvePropertyIdByPostKey($iblockId, $key);
			if ($propId <= 0)
			{
				continue;
			}

			$propRow = CIBlockProperty::GetByID($propId)->Fetch();
			if (!is_array($propRow) || (int)$propRow['IBLOCK_ID'] !== $iblockId)
			{
				continue;
			}

			if (!self::isAjaxImagePropertyRow($propRow))
			{
				continue;
			}

			if (self::isDeleteFlagged($propRow))
			{
				continue;
			}

			$fileId = self::extractFileIdFromPropPost($data);
			if ($fileId > 0)
			{
				$result[$propId] = $fileId;
			}
		}

		return $result;
	}

	private static function resolvePropertyIdByPostKey($iblockId, $key)
	{
		if (is_numeric($key) && (int)$key > 0)
		{
			return (int)$key;
		}

		$iblockId = (int)$iblockId;
		if ($iblockId <= 0 || !is_string($key) || $key === '')
		{
			return 0;
		}

		$prop = CIBlockProperty::GetList(
			array(),
			array('IBLOCK_ID' => $iblockId, 'CODE' => $key, 'ACTIVE' => 'Y')
		)->Fetch();

		return is_array($prop) ? (int)$prop['ID'] : 0;
	}

	public static function collectAllFileIdsFromPost($iblockId)
	{
		$result = array();
		$iblockId = (int)$iblockId;
		$props = self::getAjaxImageProperties($iblockId);

		$byCode = array();
		foreach ($props as $id => $prop)
		{
			$code = (string)($prop['CODE'] ?? '');
			if ($code !== '')
			{
				$byCode[$code] = (int)$id;
			}
		}

		$propData = array();
		if (!empty($_POST['PROP']) && is_array($_POST['PROP']))
		{
			$propData = $_POST['PROP'];
		}
		elseif (!empty($_REQUEST['PROP']) && is_array($_REQUEST['PROP']))
		{
			$propData = $_REQUEST['PROP'];
		}

		foreach ($propData as $key => $data)
		{
			$propId = 0;
			if (isset($props[(int)$key]))
			{
				$propId = (int)$key;
			}
			elseif (isset($byCode[$key]))
			{
				$propId = $byCode[$key];
			}

			if ($propId <= 0 || self::isDeleteFlagged($props[$propId]))
			{
				continue;
			}

			$fileId = self::extractFileIdFromPropPost($data);
			if ($fileId > 0)
			{
				$result[$propId] = $fileId;
			}
		}

		return $result;
	}

	private static function detectRowKeyFromPost($propId)
	{
		$propId = (int)$propId;
		if ($propId <= 0 || empty($_POST['PROP'][$propId]) || !is_array($_POST['PROP'][$propId]))
		{
			return null;
		}

		foreach ($_POST['PROP'][$propId] as $rowKey => $row)
		{
			if (!is_array($row))
			{
				continue;
			}
			if (!empty($row['VALUE']))
			{
				return (string)$rowKey;
			}
		}

		return null;
	}

	private static function valueToFileId($value)
	{
		if (!is_array($value))
		{
			return (int)self::parseValuePayload($value)['file_id'];
		}

		if (isset($value['VALUE_NUM']) && (string)$value['VALUE_NUM'] !== '')
		{
			return (int)self::parseValuePayload($value['VALUE_NUM'])['file_id'];
		}

		return (int)self::unpackPropertyValue($value)['file_id'];
	}

	private static function extractFileIdFromValue($arValue)
	{
		if (!is_array($arValue))
		{
			return (int)self::parseValuePayload($arValue)['file_id'];
		}

		if (!isset($arValue['VALUE']))
		{
			return (int)self::unpackPropertyValue($arValue)['file_id'];
		}

		return (int)self::parseValuePayload($arValue['VALUE'])['file_id'];
	}

	private static function restoreFileIdFromRequest($arProperty)
	{
		$propId = (int)($arProperty['ID'] ?? 0);
		if ($propId <= 0)
		{
			return 0;
		}

		$sources = array();
		if (!empty($_POST['PROP'][$propId]))
		{
			$sources[] = $_POST['PROP'][$propId];
		}
		if (!empty($_REQUEST['PROP'][$propId]))
		{
			$postProp = $_POST['PROP'][$propId] ?? null;
			if ($postProp !== $_REQUEST['PROP'][$propId])
			{
				$sources[] = $_REQUEST['PROP'][$propId];
			}
		}

		foreach ($sources as $data)
		{
			$fileId = self::extractFileIdFromPropPost($data);
			if ($fileId > 0)
			{
				return $fileId;
			}
		}

		$keys = array(
			'PROPERTY_' . $propId,
			'property_' . $propId,
		);
		foreach ($keys as $key)
		{
			if (!empty($_POST[$key]))
			{
				return (int)$_POST[$key];
			}
			if (!empty($_REQUEST[$key]))
			{
				return (int)$_REQUEST[$key];
			}
		}

		return 0;
	}

	private static function extractFileIdFromPropPost($data)
	{
		if (!is_array($data))
		{
			return (int)$data;
		}

		$valueRaw = self::findValuePayloadInPropPost($data);
		if ($valueRaw !== null && $valueRaw !== '')
		{
			$fileId = (int)self::parseValuePayload($valueRaw, 0)['file_id'];
			if ($fileId > 0)
			{
				return $fileId;
			}
			if (preg_match('/^\d+$/', trim($valueRaw)))
			{
				return (int)$valueRaw;
			}
		}

		foreach ($data as $rowKey => $row)
		{
			if ($rowKey === 'VALUE' || $rowKey === 'DESCRIPTION')
			{
				continue;
			}

			if (is_array($row))
			{
				if (isset($row['VALUE_del']) && (string)$row['VALUE_del'] === 'Y')
				{
					continue;
				}
				if (isset($row['VALUE']) && (string)$row['VALUE'] !== '' && (string)$row['VALUE'] !== '0')
				{
					$fileId = (int)self::parseValuePayload((string)$row['VALUE'])['file_id'];
					if ($fileId > 0)
					{
						return $fileId;
					}
					if (preg_match('/^\d+$/', trim((string)$row['VALUE'])))
					{
						return (int)$row['VALUE'];
					}
				}
			}
			elseif (is_numeric($row) && (int)$row > 0)
			{
				return (int)$row;
			}
		}

		return 0;
	}

	private static function isAjaxImagePropertyRow($propRow)
	{
		return is_array($propRow) && (string)($propRow['USER_TYPE'] ?? '') === self::USER_TYPE;
	}

	private static function filterFileIdsForAjaxImageProps($iblockId, array $fileIds)
	{
		$ajaxProps = self::getAjaxImageProperties((int)$iblockId);
		if (empty($ajaxProps))
		{
			return array();
		}

		$result = array();
		foreach ($fileIds as $propId => $fileId)
		{
			$propId = (int)$propId;
			if ($propId <= 0 || !isset($ajaxProps[$propId]))
			{
				continue;
			}
			$fileId = (int)$fileId;
			if ($fileId > 0)
			{
				$result[$propId] = $fileId;
			}
		}

		return $result;
	}

	private static function mergePayloadWithStoredPoints($iblockId, $elementId, $propId, array $payload)
	{
		$iblockId = (int)$iblockId;
		$elementId = (int)$elementId;
		$propId = (int)$propId;

		if ($iblockId <= 0 || $elementId <= 0 || $propId <= 0)
		{
			return $payload;
		}

		if (!empty($payload['points']))
		{
			return $payload;
		}

		$stored = self::getStoredValuePayloadForElement($iblockId, $elementId, $propId);
		if (!empty($stored['points']))
		{
			$payload['points'] = $stored['points'];
		}

		return $payload;
	}

	private static function getAjaxImageProperties($iblockId)
	{
		static $cache = array();
		$iblockId = (int)$iblockId;

		if ($iblockId <= 0)
		{
			return array();
		}

		if (isset($cache[$iblockId]))
		{
			return $cache[$iblockId];
		}

		$cache[$iblockId] = array();

		$rs = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array(
				'IBLOCK_ID' => $iblockId,
				'USER_TYPE' => self::USER_TYPE,
				'ACTIVE' => 'Y',
			)
		);
		while ($prop = $rs->Fetch())
		{
			$cache[$iblockId][(int)$prop['ID']] = $prop;
		}

		return $cache[$iblockId];
	}

	private static function resolveElementIdFromRequest()
	{
		return (int)($_POST['ID'] ?? $_POST['id'] ?? $_REQUEST['ID'] ?? $_REQUEST['id'] ?? 0);
	}

	private static function getSessionKey()
	{
		return 'AJAX_IMAGE_PROP_PENDING';
	}

	private static function setPendingFileId($elementId, $propId, $fileId)
	{
		$propId = (int)$propId;
		$fileId = (int)$fileId;
		if ($propId <= 0 || $fileId <= 0)
		{
			return;
		}

		if (!isset($_SESSION) || !is_array($_SESSION))
		{
			return;
		}

		$key = self::getSessionKey();
		if (!isset($_SESSION[$key]) || !is_array($_SESSION[$key]))
		{
			$_SESSION[$key] = array();
		}

		$elementKey = (int)$elementId > 0 ? (int)$elementId : 0;
		if (!isset($_SESSION[$key][$elementKey]) || !is_array($_SESSION[$key][$elementKey]))
		{
			$_SESSION[$key][$elementKey] = array();
		}

		$_SESSION[$key][$elementKey][$propId] = $fileId;
	}

	private static function getPendingFileId($elementId, $propId)
	{
		$propId = (int)$propId;
		if ($propId <= 0 || !isset($_SESSION) || !is_array($_SESSION))
		{
			return 0;
		}

		$key = self::getSessionKey();
		if (!isset($_SESSION[$key]) || !is_array($_SESSION[$key]))
		{
			return 0;
		}

		$elementId = (int)$elementId;
		if ($elementId > 0 && !empty($_SESSION[$key][$elementId][$propId]))
		{
			return (int)$_SESSION[$key][$elementId][$propId];
		}

		if (!empty($_SESSION[$key][0][$propId]))
		{
			return (int)$_SESSION[$key][0][$propId];
		}

		return 0;
	}

	private static function clearPendingFileId($elementId, $propId)
	{
		$propId = (int)$propId;
		if ($propId <= 0 || !isset($_SESSION[self::getSessionKey()]))
		{
			return;
		}

		$elementId = (int)$elementId;
		unset($_SESSION[self::getSessionKey()][$elementId][$propId], $_SESSION[self::getSessionKey()][0][$propId]);
	}

	private static function getStoredFileIdForElement($iblockId, $elementId, $propId)
	{
		$iblockId = (int)$iblockId;
		$elementId = (int)$elementId;
		$propId = (int)$propId;

		if ($iblockId <= 0 || $elementId <= 0 || $propId <= 0)
		{
			return 0;
		}

		if (!\Bitrix\Main\Loader::includeModule('iblock'))
		{
			return 0;
		}

		$rs = CIBlockElement::GetProperty($iblockId, $elementId, array('sort' => 'asc'), array('ID' => $propId));
		while ($row = $rs->Fetch())
		{
			$fileId = self::valueToFileId($row);
			if ($fileId > 0)
			{
				return $fileId;
			}
		}

		return 0;
	}

	private static function buildPropertyValuesExEntry($propRow, $saveValue, $propId)
	{
		$saveValue = (string)$saveValue;
		$isMultiple = is_array($propRow) && (string)($propRow['MULTIPLE'] ?? 'N') === 'Y';

		if (!$isMultiple)
		{
			return $saveValue;
		}

		$rowKey = self::detectRowKeyFromPost((int)$propId) ?? 'n0';

		return array(
			$rowKey => array(
				'VALUE' => $saveValue,
				'DESCRIPTION' => '',
			),
		);
	}

	private static function buildPropertyValuesByIdEntry($propRow, $saveValue, $propId)
	{
		$saveValue = (string)$saveValue;
		$isMultiple = is_array($propRow) && (string)($propRow['MULTIPLE'] ?? 'N') === 'Y';

		if (!$isMultiple)
		{
			return $saveValue;
		}

		$rowKey = self::detectRowKeyFromPost((int)$propId) ?? 'n0';

		return array(
			$rowKey => array(
				'VALUE' => $saveValue,
				'DESCRIPTION' => '',
			),
		);
	}

	private static function writePropertyValue($elementId, $iblockId, $propId, $propCode, $fileId, $points = null)
	{
		$propId = (int)$propId;
		$elementId = (int)$elementId;
		$iblockId = (int)$iblockId;

		if ($propId <= 0)
		{
			return;
		}

		$propRow = CIBlockProperty::GetByID($propId)->Fetch();
		if (!is_array($propRow) || !self::isAjaxImagePropertyRow($propRow))
		{
			return;
		}

		$linkIblockId = self::getLinkIblockId($propRow);

		if ($points === null && $elementId > 0 && $iblockId > 0)
		{
			$stored = self::getStoredValuePayloadForElement($iblockId, $elementId, $propId);
			$points = $stored['points'];
		}
		elseif (!is_array($points))
		{
			$points = array();
		}

		$saveValue = self::encodeValuePayload($fileId, $points, $linkIblockId);

		if ($propCode === '')
		{
			$propCode = (string)($propRow['CODE'] ?? '');
		}

		if ($fileId === false || $fileId === null || (int)$fileId <= 0)
		{
			if ($propCode !== '')
			{
				CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, array($propCode => false));
			}
			else
			{
				CIBlockElement::SetPropertyValues($elementId, $iblockId, false, $propId);
			}

			return;
		}

		if ($propCode !== '')
		{
			CIBlockElement::SetPropertyValuesEx(
				$elementId,
				$iblockId,
				array(
					$propCode => self::buildPropertyValuesExEntry($propRow, $saveValue, $propId),
				)
			);
		}
		else
		{
			CIBlockElement::SetPropertyValues(
				$elementId,
				$iblockId,
				self::buildPropertyValuesByIdEntry($propRow, $saveValue, $propId),
				$propId
			);
		}

		if (class_exists('\Bitrix\Iblock\PropertyIndex\Manager'))
		{
			\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblockId, $elementId);
		}
	}
}

