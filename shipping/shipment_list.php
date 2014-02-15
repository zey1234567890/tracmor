<?php
/*
 * Copyright (c)  2009, Tracmor, LLC
 *
 * This file is part of Tracmor.
 *
 * Tracmor is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tracmor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tracmor; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
	require_once('../includes/prepend.inc.php');
	QApplication::Authenticate(5);
	require_once(__FORMBASE_CLASSES__ . '/ShipmentListFormBase.class.php');
    require('../shipping/shipmentMassEditPanel.class.php');
	/**
	 * This is a quick-and-dirty draft form object to do the List All functionality
	 * of the Shipment class.  It extends from the code-generated
	 * abstract ShipmentListFormBase class.
	 *
	 * Any display custimizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * Additional qform control objects can also be defined and used here, as well.
	 * 
	 * @package Application
	 * @subpackage FormDraftObjects
	 * 
	 */
	class ShipmentListForm extends ShipmentListFormBase {

	/**
	 * @var  QLabel     $lblWarning
	 * @var  QDialogBox $dlgMassEdit
	 * @var  QDialogBox $dlgMassDelete
	 * @var  QButton    $btnMassDelete
	 * @var  QButton    $btnMassEdit
	 *
	 */
		// Header Tabs
		protected $ctlHeaderMenu;
		
		// Shortcut Menu
		protected $ctlShortcutMenu;		

		// Basic Inputs
		protected $txtToCompany;
		protected $txtToContact;
		protected $txtShipmentNumber;
		protected $txtAssetCode;
		protected $txtInventoryModelCode;
		protected $lstStatus;
		
		/// Buttons
		protected $btnSearch;
		protected $blnSearch;
		protected $btnClear;
		
		// Advanced Label/Link
		protected $lblAdvanced;
		// Boolean that toggles Advanced Search display
		protected $blnAdvanced;
		// Advanced Search Composite control
		protected $ctlAdvanced;

		// Search Values
		protected $strToCompany;
		protected $strToContact;
		protected $strFromCompany;
		protected $strFromContact;
		protected $strShipmentNumber;
		protected $strAssetCode;
		protected $strInventoryModelCode;
		protected $intStatus;
		protected $strTrackingNumber;
		protected $intCourierId;
		protected $strNote;
		protected $strShipmentDate;
		protected $strDateModified;
		protected $strDateModifiedFirst;
		protected $strDateModifiedLast;
		protected $blnAttachment;
		
		// Custom Fields array
		protected $arrCustomFields;
		
		// HoverTip Arrays
		public $objAssetTransactionArray;
		public $objInventoryTransactionArray;

		// Mass Actions Elements
		protected $lblWarning;
		protected $dlgMassEdit;
		protected $dlgMassDelete;
		protected $btnMassEdit;
		protected $btnMassDelete;
		protected $pnlShipmentMassEdit;
        protected $btnMassDeleteConfirm;
        protected $btnMassDeleteCancel;

        protected $arrToDelete = array();

		protected function Form_Create() {
			
			//QApplication::$Database[1]->EnableProfiling();
			
			// Create the Header Menu
			$this->ctlHeaderMenu_Create();
			// Create the Shortcut Menu
			$this->ctlShortcutMenu_Create();			
			$this->txtToCompany_Create();
			$this->txtToContact_Create();
			$this->txtShipmentNumber_Create();
			$this->txtAssetCode_Create();
			$this->txtInventoryModelCode_Create();
			$this->lstStatus_Create();
			$this->btnSearch_Create();
			$this->btnClear_Create();
			$this->ctlAdvanced_Create();
			$this->lblAdvanced_Create();
			$this->dtgShipment_Create();

			// Mass Actions controls create
			$this->lblWarning_Create();
			$this->dlgMassEdit_Create();
			$this->dlgMassDelete_Create();
            $this->btnMassDeleteCancel_Create();
            $this->btnMassDeleteConfirm_Create();
			$this->btnMassDelete_Create();
			$this->btnMassEdit_Create();

		}
		
		//protected function Form_Exit() {
			//QApplication::$Database[1]->OutputProfiling();
		//}
		
		protected function dtgShipment_Bind() {
			
			// Assing the class member values from the search form inputs
			if ($this->blnSearch) {
				$this->assignSearchValues();
			}			
			
			// Assign local method variables
			$strToCompany = $this->strToCompany;
			$strToContact = $this->strToContact;
			$strFromCompany = $this->strFromCompany;
			$strFromContact = $this->strFromContact;
			$strShipmentNumber = $this->strShipmentNumber;
			$strAssetCode = $this->strAssetCode;
			$strInventoryModelCode = $this->strInventoryModelCode;
			$intStatus = $this->intStatus;
			$strTrackingNumber = $this->strTrackingNumber;
			$intCourierId = $this->intCourierId;
			$strNote = $this->strNote;
			$strShipmentDate = $this->strShipmentDate;
			$strDateModifiedFirst = $this->strDateModifiedFirst;
			$strDateModifiedLast = $this->strDateModifiedLast;
			$strDateModified = $this->strDateModified;
			$blnAttachment = $this->blnAttachment;
			$arrCustomFields = $this->arrCustomFields;
			
			// Expand to include the primary address, State/Province, and Country
			$objExpansionMap[Shipment::ExpandTransaction] = true;
			$objExpansionMap[Shipment::ExpandToCompany] = true;
			$objExpansionMap[Shipment::ExpandToContact] = true;
			$objExpansionMap[Shipment::ExpandFromCompany] = true;
			$objExpansionMap[Shipment::ExpandFromContact] = true;
			$objExpansionMap[Shipment::ExpandFromAddress] = true;
			$objExpansionMap[Shipment::ExpandToAddress] = true;
			$objExpansionMap[Shipment::ExpandCourier] = true;
			$objExpansionMap[Shipment::ExpandCreatedByObject] = true;
			
			// QApplication::$Database[1]->EnableProfiling();
			
			$this->dtgShipment->TotalItemCount = Shipment::CountBySearchHelper($strToCompany, $strToContact, $strFromCompany, $strFromContact, $strShipmentNumber, $strAssetCode, $strInventoryModelCode, $intStatus, $strTrackingNumber, $intCourierId, $strNote, $strShipmentDate, $arrCustomFields, $strDateModified, $strDateModifiedFirst, $strDateModifiedLast, $blnAttachment, $objExpansionMap);
			if ($this->dtgShipment->TotalItemCount == 0) {
				$this->dtgShipment->ShowHeader = false;
			}
			else {
				$this->dtgShipment->DataSource = Shipment::LoadArrayBySearchHelper($strToCompany, $strToContact, $strFromCompany, $strFromContact, $strShipmentNumber, $strAssetCode, $strInventoryModelCode, $intStatus, $strTrackingNumber, $intCourierId, $strNote, $strShipmentDate, $arrCustomFields, $strDateModified, $strDateModifiedFirst, $strDateModifiedLast, $blnAttachment, $this->dtgShipment->SortInfo, $this->dtgShipment->LimitInfo, $objExpansionMap);
				$this->dtgShipment->ShowHeader = true;
			}
			$this->blnSearch = false;
		}
		
		// protected function Form_Exit() {
			// QApplication::$Database[1]->OutputProfiling();
		// }
		
  	// Create and Setup the Header Composite Control
  	protected function ctlHeaderMenu_Create() {
  		$this->ctlHeaderMenu = new QHeaderMenu($this);
  	}

  	// Create and Setp the Shortcut Menu Composite Control
  	protected function ctlShortcutMenu_Create() {
  		$this->ctlShortcutMenu = new QShortcutMenu($this);
  	}		
		
		protected function txtToCompany_Create() {
			$this->txtToCompany = new QTextBox($this);
			$this->txtToCompany->Name = 'Recipient Company';
			$this->txtToCompany->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->txtToCompany->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}
		
		protected function txtToContact_Create() {
			$this->txtToContact = new QTextBox($this);
			$this->txtToContact->Name = 'Recipient Contact';
			$this->txtToContact->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->txtToContact->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}	
		
		protected function txtShipmentNumber_Create() {
			$this->txtShipmentNumber = new QTextBox($this);
			$this->txtShipmentNumber->Name = 'Shipment Number';
			$this->txtShipmentNumber->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->txtShipmentNumber->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}
		
		protected function txtAssetCode_Create() {
			$this->txtAssetCode = new QTextBox($this);
			$this->txtAssetCode->Name = 'Asset Tag';
			$this->txtAssetCode->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->txtAssetCode->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}
		
		protected function txtInventoryModelCode_Create() {
			$this->txtInventoryModelCode = new QTextBox($this);
			$this->txtInventoryModelCode->Name = 'Inventory Code';
			$this->txtInventoryModelCode->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->txtInventoryModelCode->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function lstStatus_Create() {
			$this->lstStatus = new QListBox($this);
			$this->lstStatus->Name = 'Status';
			$this->lstStatus->AddItem('- Select One -', null);
			$this->lstStatus->AddItem('Pending', 1);
			$this->lstStatus->AddItem('Shipped', 2);
			$this->lstStatus->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->lstStatus->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}
		
	  /**************************
	   *	CREATE BUTTON METHODS
	  **************************/
		
	  // Create the Search Button
	  protected function btnSearch_Create() {
			$this->btnSearch = new QButton($this);
			$this->btnSearch->Name = 'search';
			$this->btnSearch->Text = 'Search';
			$this->btnSearch->AddAction(new QClickEvent(), new QServerAction('btnSearch_Click'));
			$this->btnSearch->AddAction(new QEnterKeyEvent(), new QServerAction('btnSearch_Click'));
			$this->btnSearch->AddAction(new QEnterKeyEvent(), new QTerminateAction());
	  }
	  
	  // Create the Clear Button
	  protected function btnClear_Create() {
	  	$this->btnClear = new QButton($this);
			$this->btnClear->Name = 'clear';
			$this->btnClear->Text = 'Clear';
			$this->btnClear->AddAction(new QClickEvent(), new QServerAction('btnClear_Click'));
			$this->btnClear->AddAction(new QEnterKeyEvent(), new QServerAction('btnClear_Click'));
			$this->btnClear->AddAction(new QEnterKeyEvent(), new QTerminateAction());			
	  }
	  
	  // Create the 'Advanced Search' Label
	  protected function lblAdvanced_Create() {
	  	$this->lblAdvanced = new QLabel($this);
	  	$this->lblAdvanced->Name = 'Advanced';
	  	$this->lblAdvanced->Text = 'Advanced Search';
	  	$this->lblAdvanced->AddAction(new QClickEvent(), new QToggleDisplayAction($this->ctlAdvanced));
	  	$this->lblAdvanced->AddAction(new QClickEvent(), new QAjaxAction('lblAdvanced_Click'));
	  	$this->lblAdvanced->SetCustomStyle('text-decoration', 'underline');
	  	$this->lblAdvanced->SetCustomStyle('cursor', 'pointer');
	  }
	  
	  // Create the Advanced Search Composite Control
  	protected function ctlAdvanced_Create() {
  		$this->ctlAdvanced = new QAdvancedSearchComposite($this, 10);
  		$this->ctlAdvanced->Display = false;
  	}
	  
	  // Create the Shipment datagrid
  	protected function dtgShipment_Create() {
		$this->dtgShipment = new QDataGrid($this);
		$this->dtgShipment->Name = 'shipment_list';
  		$this->dtgShipment->CellPadding = 5;
  		$this->dtgShipment->CellSpacing = 0;
  		$this->dtgShipment->CssClass = "datagrid";
  		
  		// Allow for column toggling
  		$this->dtgShipment->ShowColumnToggle = true;
  		
  		// Allow for CSV Export
  		$this->dtgShipment->ShowExportCsv = true;
      		
      // Disable AJAX on the datagrid
      $this->dtgShipment->UseAjax = false;

      // Enable Pagination
      $objPaginator = new QPaginator($this->dtgShipment);
      $this->dtgShipment->Paginator = $objPaginator;
      $this->dtgShipment->ItemsPerPage = QApplication::$TracmorSettings->SearchResultsPerPage;

	  $this->dtgShipment->AddColumn(new QDataGridColumnExt('<?= $_CONTROL->chkSelectAll_Render() ?>', '<?=$_CONTROL->chkSelected_Render($_ITEM->ShipmentId) ?>', 'CssClass="dtg_column"', 'HtmlEntities=false'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('<img src=../images/icons/attachment_gray.gif border=0 title=Attachments alt=Attachments>', '<?= Attachment::toStringIcon($_ITEM->GetVirtualAttribute(\'attachment_count\')); ?>', 'SortByCommand="__attachment_count ASC"', 'ReverseSortByCommand="__attachment_count DESC"', 'CssClass="dtg_column"', 'HtmlEntities="false"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Shipment Number', '<?= $_ITEM->__toStringWithLink("bluelink") ?> <?= $_ITEM->__toStringHoverTips($_CONTROL) ?>', 'SortByCommand="shipment_number * 1 ASC"', 'ReverseSortByCommand="shipment_number * 1 DESC"', 'CssClass="dtg_column"', 'HtmlEntities=false'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Ship Date', '<?= $_ITEM->ShipDate->__toString(); ?>', 'SortByCommand="ship_date ASC"', 'ReverseSortByCommand="ship_date DESC"', 'CssClass="dtg_column"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Recipient Company', '<?= $_ITEM->ToCompany->__toString() ?>', 'Width=200', 'SortByCommand="shipment__to_company_id__short_description ASC"', 'ReverseSortByCommand="shipment__to_company_id__short_description DESC"', 'CssClass="dtg_column"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Recipient Contact', '<?= $_ITEM->ToContact->__toString() ?>', 'SortByCommand="shipment__to_contact_id__last_name ASC"', 'ReverseSortByCommand="shipment__to_contact_id__last_name DESC"', 'CssClass="dtg_column"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Recipient Address', '<?= $_ITEM->ToAddress->__toString() ?>', 'SortByCommand="shipment__to_address_id__short_description ASC"', 'ReverseSortByCommand="shipment__to_address_id__short_description DESC"', 'CssClass="dtg_column"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Sender Company', '<?= $_ITEM->FromCompany->__toString() ?>', 'Width=200', 'SortByCommand="shipment__from_company_id__short_description ASC"', 'ReverseSortByCommand="shipment__from_company_id__short_description DESC"', 'CssClass="dtg_column"', 'Display="false"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Sender Contact', '<?= $_ITEM->FromContact->__toString() ?>', 'SortByCommand="shipment__from_contact_id__last_name ASC"', 'ReverseSortByCommand="shipment__from_contact_id__last_name DESC"', 'CssClass="dtg_column"', 'Display="false"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Sender Address', '<?= $_ITEM->FromAddress->__toString() ?>', 'SortByCommand="shipment__from_address_id__short_description ASC"', 'ReverseSortByCommand="shipment__from_address_id__short_description DESC"', 'CssClass="dtg_column"', 'Display="false"'));      
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Scheduled By', '<?= $_ITEM->CreatedByObject->__toString() ?>', 'SortByCommand="shipment__created_by__last_name ASC"', 'ReverseSortByCommand="shipment__created_by__last_name DESC"', 'CssClass="dtg_column"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Status', '<?= $_ITEM->__toStringStatusStyled() ?>', 'SortByCommand="shipped_flag ASC"', 'ReverseSortByCommand="shipped_flag DESC"', 'CssClass="dtg_column"', 'HtmlEntities=false'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Tracking', '<?= $_ITEM->__toStringTrackingNumber() ?>', 'CssClass="dtg_column"', 'HtmlEntities=false'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Courier', '<?= $_ITEM->__toStringCourier() ?>', 'SortByCommand="shipment__courier_id__short_description ASC"', 'ReverseSortByCommand="shipment__courier_id__short_description DESC"', 'CssClass="dtg_column"', 'HtmlEntities="false"', 'Display="false"'));
      $this->dtgShipment->AddColumn(new QDataGridColumnExt('Note', '<?= $_ITEM->Transaction->Note ?>', 'SortByCommand="shipment__transaction_id__note ASC"', 'ReverseSortByCommand="shipment__transaction_id__note DESC"', 'CssClass="dtg_column"', 'Width="160"', 'HtmlEntities="false"', 'Display="false"'));
      
      // Add the custom field columns with Display set to false. These can be shown by using the column toggle menu.
      $objCustomFieldArray = CustomField::LoadObjCustomFieldArray(10, false);
      if ($objCustomFieldArray) {
      	foreach ($objCustomFieldArray as $objCustomField) {
      		//Only add the custom field column if the role has authorization to view it.
      		if($objCustomField->objRoleAuthView && $objCustomField->objRoleAuthView->AuthorizedFlag)
      			$this->dtgShipment->AddColumn(new QDataGridColumnExt($objCustomField->ShortDescription, '<?= $_ITEM->GetVirtualAttribute(\''.$objCustomField->CustomFieldId.'\') ?>', 'SortByCommand="__'.$objCustomField->CustomFieldId.' ASC"', 'ReverseSortByCommand="__'.$objCustomField->CustomFieldId.' DESC"','HtmlEntities="false"', 'CssClass="dtg_column"', 'Display="false"'));
      	}
      }
      
      $this->dtgShipment->SortColumnIndex = 1;
    	$this->dtgShipment->SortDirection = 1;
      
      $objStyle = $this->dtgShipment->RowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#FFFFFF';
      $objStyle->FontSize = 12;

      $objStyle = $this->dtgShipment->AlternateRowStyle;
      $objStyle->BackColor = '#EFEFEF';

      $objStyle = $this->dtgShipment->HeaderRowStyle;
      $objStyle->ForeColor = '#000000';
      $objStyle->BackColor = '#EFEFEF';
      $objStyle->CssClass = 'dtg_header';
      
      $this->dtgShipment->SetDataBinder('dtgShipment_Bind');
  	}
  	
	  protected function btnSearch_Click() {
	  	$this->blnSearch = true;
			$this->dtgShipment->PageNumber = 1;
	  }
	  
	  protected function btnClear_Click() {

  		// Set controls to null
	  	$this->txtToCompany->Text = '';
	  	$this->txtToContact->Text = '';
	  	$this->txtShipmentNumber->Text = '';
	  	$this->txtAssetCode->Text = '';
	  	$this->txtInventoryModelCode->Text = '';
	  	$this->lstStatus->SelectedIndex = 0;
	  	$this->ctlAdvanced->ClearControls();
	  	
	  	// Set search variables to null
	  	$this->strToCompany = null;
	  	$this->strToContact = null;
	  	$this->strFromCompany = null;
	  	$this->strFromContact = null;
	  	$this->strShipmentNumber = null;
	  	$this->strAssetCode = null;
	  	$this->strInventoryModelCode = null;
	  	$this->intStatus = null;
	  	$this->strTrackingNumber = null;
	  	$this->intCourierId = null;
	  	$this->strNote = null;
	  	$this->strShipmentDate = null;
	  	$this->strDateModified = null;
	  	$this->strDateModifiedFirst = null;
	  	$this->strDateModifiedLast = null;
	  	$this->blnAttachment = false;
	  	if ($this->arrCustomFields) {
	  		foreach ($this->arrCustomFields as $field) {
	  			$field['value'] = null;
	  		}
	  	}
	  	$this->blnSearch = false;
  	}
  	
  	// Display or hide the Advanced Search Composite Control
	  protected function lblAdvanced_Click() {
	  	if ($this->blnAdvanced) {
	  		$this->blnAdvanced = false;
	  		$this->lblAdvanced->Text = 'Advanced Search';
	  		
	  		//$this->ctlAdvanced->ClearControls();
	  	}
	  	else {
	  		$this->blnAdvanced = true;
	  		$this->lblAdvanced->Text = 'Hide Advanced';
	  	}
	  }

	  // Assign the search variables values from the form inputs
	  protected function assignSearchValues() {
	  	
	  	$this->strToCompany = $this->txtToCompany->Text;
	  	$this->strToContact = $this->txtToContact->Text;
	  	$this->strShipmentNumber = $this->txtShipmentNumber->Text;
	  	$this->strAssetCode = $this->txtAssetCode->Text;
	  	$this->strInventoryModelCode = $this->txtInventoryModelCode->Text;
	  	$this->intStatus = $this->lstStatus->SelectedValue;
	  	$this->strFromCompany = $this->ctlAdvanced->FromCompany;
	  	$this->strFromContact = $this->ctlAdvanced->FromContact;	  	
	  	$this->strTrackingNumber = $this->ctlAdvanced->TrackingNumber;
	  	$this->intCourierId = $this->ctlAdvanced->CourierId;
	  	$this->strNote = $this->ctlAdvanced->Note;
	  	$this->strShipmentDate = $this->ctlAdvanced->ShipmentDate;
			$this->strDateModified = $this->ctlAdvanced->DateModified;
			$this->strDateModifiedFirst = $this->ctlAdvanced->DateModifiedFirst;
			$this->strDateModifiedLast = $this->ctlAdvanced->DateModifiedLast;
			$this->blnAttachment = $this->ctlAdvanced->Attachment;
			
			$this->arrCustomFields = $this->ctlAdvanced->CustomFieldArray;
			if ($this->arrCustomFields) {
				foreach ($this->arrCustomFields as &$field) {
					if ($field['input'] instanceof QListBox) {
						$field['value'] = $field['input']->SelectedValue;
					}
					elseif ($field['input'] instanceof QTextBox) {
						$field['value'] = $field['input']->Text;
					}
				}
			}
	  }
		// Mass Actions controls creating/handling functions
		protected function dlgMassDelete_Create(){
			$this->dlgMassDelete = new QDialogBox($this);
			$this->dlgMassDelete->AutoRenderChildren = true;
			$this->dlgMassDelete->Width = '440px';
			$this->dlgMassDelete->Overflow = QOverflow::Auto;
			$this->dlgMassDelete->Padding = '10px';
			$this->dlgMassDelete->Display = false;
			$this->dlgMassDelete->BackColor = '#FFFFFF';
			$this->dlgMassDelete->MatteClickable = false;
			$this->dlgMassDelete->CssClass = "modal_dialog";
		}

		protected function dlgMassEdit_Create(){
			$this->dlgMassEdit = new QDialogBox($this);
			$this->dlgMassEdit->AutoRenderChildren = true;
			$this->dlgMassEdit->Width = '440px';
			$this->dlgMassEdit->Overflow = QOverflow::Auto;
			$this->dlgMassEdit->Padding = '10px';
			$this->dlgMassEdit->Display = false;
			$this->dlgMassEdit->BackColor = '#FFFFFF';
			$this->dlgMassEdit->MatteClickable = false;
			$this->dlgMassEdit->CssClass = "modal_dialog";
		}

		protected function btnMassDelete_Create(){
			$this->btnMassDelete = new QButton($this);
			$this->btnMassDelete->Name = "delete";
			$this->btnMassDelete->Text = "Delete";
			$this->btnMassDelete->AddAction(new QClickEvent(), new QConfirmAction('Are you sure you want to delete these items?'));
			$this->btnMassDelete->AddAction(new QClickEvent(), new QAjaxAction('btnMassDelete_Click'));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDelete_Click'));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function btnMassEdit_Create(){
			$this->btnMassEdit = new QButton($this);
			$this->btnMassEdit->Text = "edit";
			$this->btnMassEdit->Text = "Edit";
			$this->btnMassEdit->AddAction(new QClickEvent(), new  QAjaxAction('btnMassEdit_Click'));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassEdit_Click'));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function lblWarning_Create(){
			$this->lblWarning = new QLabel($this);
			$this->lblWarning->Text = "";
			$this->lblWarning->CssClass = "warning";
		}

		protected function btnMassDelete_Click(){
			$items = $this->dtgShipment->getSelected('ShipmentId');
			if(count($items)>0){
				$this->lblWarning->Text = "";
                $arrToSkip = array();
                // Separating items able to be deleted
                foreach ($items as $item){
                    $shipmentToDelete = Shipment::Load($item);
                    if ($shipmentToDelete instanceof Shipment && !($shipmentToDelete->ShippedFlag)){
                        $this->arrToDelete[] = $shipmentToDelete; // objects stored in array!
                    }
                    else{
                        $arrToSkip[] = $shipmentToDelete->ShipmentNumber;
                    }
                }
                if (count($arrToSkip)>0){
                    if(count($arrToSkip)==1){
                        $toBe = 'is';
                        $ending = '';
                    }
                    else{
                        $toBe = 'are';
                        $ending = 's';
                    }
                    $this->dlgMassDelete->Text =sprintf("There %s %s Shipment%s that %s not able to be deleted.
                                                         Would you like to continue the deletion process,
                                                         skipping these item%s?<br />",
                                                         $toBe, count($arrToSkip), $ending, $toBe, $ending);
                    $this->dlgMassDelete->ShowDialogBox();
                }
                else{
                    if (count($this->arrToDelete)>0){
                        try{
                            // Get an instance of the database
                            $objDatabase = QApplication::$Database[1];
                            // Begin a MySQL Transaction to be either committed or rolled back
                            $objDatabase->TransactionBegin();
                            foreach($this->arrToDelete as $shipment){
                                $objTransaction = Transaction::Load($shipment->TransactionId);
                                $objTransaction->Delete();
                            }
                            $objDatabase->TransactionCommit();
                            $this->arrToDelete = array();
                            QApplication::Redirect('');
                        }
                        catch(QMySqliDatabaseException $objExc) {
                            $objDatabase->TransactionRollback();
                            throw new QDatabaseException();
                        }
                    }
                }
			}else{
				$this->lblWarning->Text = "You haven't chosen any Shipment to Delete" ;
			}
		}

		protected function btnMassEdit_Click(){
			$items = $this->dtgShipment->getSelected('ShipmentId');
			if(count($items)>0){
				$this->lblWarning->Text = "";
				if(!($this->pnlShipmentMassEdit instanceof ShipmentMassEditPanel)){
					$this->pnlShipmentMassEdit = new ShipmentMassEditPanel($this->dlgMassEdit,
						                                                   'pnlShipmentMassEdit_Close',
					                                                       $items);
				}
				$this->dlgMassEdit->ShowDialogBox();
			}else{
				$this->lblWarning->Text = "You haven't chosen any Shipment to Edit" ;
			}
		}
		public function pnlShipmentMassEdit_Close(){
			$this->dlgMassEdit->HideDialogBox();
		}

        protected function btnMassDeleteCancel_Create(){
            $this->btnMassDeleteCancel = new QButton($this->dlgMassDelete);
            $this->btnMassDeleteCancel->Text = "Cancel";
            $this->btnMassDeleteCancel->AddAction(new QClickEvent(), new QAjaxAction('btnMassDeleteCancel_Click'));
            $this->btnMassDeleteCancel->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDeleteCancel_Click'));
        }

        protected function btnMassDeleteConfirm_Create(){
            $this->btnMassDeleteConfirm = new QButton($this->dlgMassDelete);
            $this->btnMassDeleteConfirm->Text = "Confirm";
            $this->btnMassDeleteConfirm->AddAction(new QClickEvent(), new QAjaxAction('btnMassDeleteConfirm_Click'));
            $this->btnMassDeleteConfirm->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDeleteConfirm_Click'));
        }

        protected function btnMassDeleteConfirm_Click(){
            if (count($this->arrToDelete)>0){
                foreach($this->arrToDelete as $shipment){
                    $objTransaction = Transaction::Load($shipment->TransactionId);
                    $objTransaction->Delete();
                }
                $this->arrToDelete = array();
            }
            $this->dlgMassDelete->HideDialogBox();
            QApplication::Redirect('');
        }

        protected function btnMassDeleteCancel_Click(){
            $this->dlgMassDelete->HideDialogBox();
            QApplication::Redirect('');
        }

        public function lstFromCompany_Select(){
            $objCompany = Company::Load($this->pnlShipmentMassEdit->lstFromCompany->SelectedValue);
            if ($objCompany) {
                // Load the values for the 'From Contact' List
                if ($this->pnlShipmentMassEdit->lstFromContact) {
                    $objFromContactArray = Contact::LoadArrayByCompanyId($objCompany->CompanyId);
                    $this->pnlShipmentMassEdit->lstFromContact->RemoveAllItems();
                    $this->pnlShipmentMassEdit->lstFromContact->AddItem('- Select One -', null);
                    if ($objFromContactArray) {
                        foreach ($objFromContactArray as $objFromContact) {
                            $objListItem = new QListItem($objFromContact->__toString(), $objFromContact->ContactId);
                            $this->pnlShipmentMassEdit->lstFromContact->AddItem($objListItem);
                        }

                        $this->pnlShipmentMassEdit->lstFromContact->Enabled = true;
                    }
                }
                if ($this->pnlShipmentMassEdit->lstFromAddress) {
                    $objFromAddressArray = Address::LoadArrayByCompanyId($objCompany->CompanyId,
                        QQ::Clause(QQ::OrderBy(QQN::Address()->ShortDescription)));
                    $this->pnlShipmentMassEdit->lstFromAddress->RemoveAllItems();
                    if ($objFromAddressArray) {
                        foreach ($objFromAddressArray as $objFromAddress) {
                            $objListItem = new QListItem($objFromAddress->__toString(),
                                $objFromAddress->AddressId);
                            $this->pnlShipmentMassEdit->lstFromAddress->AddItem($objListItem);
                        }
                        $this->pnlShipmentMassEdit->lstFromAddress->Enabled = true;
                        //$this->lstToAddress_Select();
                    }
                }
            }
        }

        public function lstToCompany_Select(){
            if ($this->pnlShipmentMassEdit->lstToCompany->SelectedValue) {
                $objCompany = Company::Load($this->pnlShipmentMassEdit->lstToCompany->SelectedValue);
                if ($objCompany) {
                    // Load the values for the 'To Contact' List
                    if ($this->pnlShipmentMassEdit->lstToContact) {
                        $objToContactArray = Contact::LoadArrayByCompanyId($objCompany->CompanyId,
                            QQ::Clause(QQ::OrderBy(QQN::Contact()->LastName,
                                    QQN::Contact()->FirstName)
                            )
                        );
                        $this->pnlShipmentMassEdit->lstToContact->RemoveAllItems();
                        if ($objToContactArray) {
                            foreach ($objToContactArray as $objToContact) {
                                $objListItem = new QListItem($objToContact->__toString(),
                                    $objToContact->ContactId);
                                $this->pnlShipmentMassEdit->lstToContact->AddItem($objListItem);
                            }
                            $this->pnlShipmentMassEdit->lstToContact->Enabled = true;
                        }
                    }
                    // Load the values for the 'To Address' List
                    if ($this->pnlShipmentMassEdit->lstToAddress) {
                        $objToAddressArray = Address::LoadArrayByCompanyId($objCompany->CompanyId,
                            QQ::Clause(QQ::OrderBy(QQN::Address()->ShortDescription)));
                        $this->pnlShipmentMassEdit->lstToAddress->RemoveAllItems();
                        if ($objToAddressArray) {
                            foreach ($objToAddressArray as $objToAddress) {
                                $objListItem = new QListItem($objToAddress->__toString(),
                                    $objToAddress->AddressId);
                                $this->pnlShipmentMassEdit->lstToAddress->AddItem($objListItem);
                            }
                            $this->pnlShipmentMassEdit->lstToAddress->Enabled = true;
                            //$this->lstToAddress_Select();
                        }
                    }
                }
            }
        }


}




	// Go ahead and run this form object to generate the page and event handlers, using
	// generated/shipment_edit.php.inc as the included HTML template file
	ShipmentListForm::Run('ShipmentListForm', __DOCROOT__ . __SUBDIRECTORY__ . '/shipping/shipment_list.tpl.php');
?>
