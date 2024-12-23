<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Routes;

use Core\Helpers\SmartGeneral as General;
use Core\Helpers\SmartConst;

class CommonRouter
{

    private $_routes = [];
    // private $_admin_only = ["ADMIN", "USER"];
    private $_admin_only = [];
    private $_user_only = ["USER"];
    // private $_admin_user = ["ADMIN", "USER"];
    private $_admin_user = [];

    /**
     * 
     */
    private function auth_routes()
    {
        $controller = "AuthController";
        $this->_routes["/auth/login"] = [SmartConst::REQUEST_POST, $controller, "login"];
        $this->_routes["/auth/user_reset"] = [SmartConst::REQUEST_POST, $controller, "userReset"];
        $this->_routes["/auth/get_log"] = [SmartConst::REQUEST_POST, $controller, "getLog"];
        $this->_routes["/auth/get_settings"] = [SmartConst::REQUEST_GET, $controller, "getSiteSettings"];
        $this->_routes["/auth/do_backup"] = [SmartConst::REQUEST_GET, $controller, "takeBackup"];
        $this->_routes["/auth/test_excel"] = [SmartConst::REQUEST_GET, $controller, "testExcel"];
        $this->_routes["/auth/create_excel"] = [SmartConst::REQUEST_GET, $controller, "createExcelFromData"];
    }


    private function users_routes()
    {
        $controller = "UserController";
        $this->_routes["/user/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/user/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/user/update_profile_img"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "updateUserProfilePic"];
        $this->_routes["/user/update_profile_details"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "updateUserProfileDetails"];
        $this->_routes["/user/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/user/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/user/get_one_user"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getOneUser"];
        $this->_routes["/user/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/user/admin_reset"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "adminReset"];
        $this->_routes["/user/user_reset"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "userReset"];
        $this->_routes["/user/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getAllSelect"];
        $this->_routes["/user/get_logged_users"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getRecentLoggedInUsers"];
        $this->_routes["/user/get_one_image"] = [SmartConst::REQUEST_POST, $controller, "getOneImage"];
    }

    private function role_routes()
    {
        $controller = "RoleController";
        $this->_routes["/role/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/role/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/role/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/role/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/role/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/role/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getAllSelect"];
    }



    private function user_role_routes()
    {
        $controller = "UserRoleController";
        $this->_routes["/userrole/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/userrole/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/userrole/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/userrole/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/userrole/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function efl_office_routes()
    {
        $controller = "EflOfficeController";
        $this->_routes["/efloffice/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/efloffice/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/efloffice/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/efloffice/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/efloffice/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/efloffice/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
    }
    private function hubs_routes()
    {
        $controller = "HubsController";
        $this->_routes["/hubs/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/hubs/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/hubs/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/hubs/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/hubs/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/hubs/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
        $this->_routes["/hubs/get_hub_id"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getHubID"];
        $this->_routes["/hubs/import_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importExcel"];
        $this->_routes["/hubs/status_update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateStatus"];
        $this->_routes["/hubs/get_all_incharge"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllIncharge"];
    }
    private function hub_groups_routes()
    {
        $controller = "HubGroupsController";
        $this->_routes["/hubgroups/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/hubgroups/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/hubgroups/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/hubgroups/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/hubgroups/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function state_db_routes()
    {
        $controller = "StateDbController";
        $this->_routes["/state_db/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/state_db/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/state_db/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/state_db/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/state_db/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/state_db/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
    }

    private function customer_routes()
    {
        $controller = "CustomerController";
        $this->_routes["/customer/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/customer/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/customer/get_all"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/customer/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/customer/update_status"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateStatus"];
        $this->_routes["/customer/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];

        $this->_routes["/customer/insert_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert_address"];
        $this->_routes["/customer/update_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update_address"];
        $this->_routes["/customer/get_all_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllAddress"];
        $this->_routes["/customer/get_one_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneAddress"];
        $this->_routes["/customer/update_status_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateAddressStatus"];
        $this->_routes["/customer/get_all_select_address"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllAddressSelect"];


        $this->_routes["/customer/migrate"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "migrate"];
    }

    private function vendors_routes()
    {
        $controller = "VendorsController";
        $this->_routes["/vendors/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/vendors/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/vendors/get_all"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/vendors/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/vendors/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/vendors/get_all_select"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllSelect"];
        $this->_routes["/vendors/get_vendor_comapany"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getVendorCompany"];
        $this->_routes["/vendors/status_update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateStatus"];
        $this->_routes["/vendors/import_excel"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "importExcel"];
    }
    private function vendor_rate_routes()
    {
        $controller = "VendorRateController";
        $this->_routes["/vendor_rate/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/vendor_rate/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/vendor_rate/get_all"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/vendor_rate/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/vendor_rate/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/vendor_rate/migrate"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "migrate"];
        $this->_routes["/vendor_rate/migrate_cust"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "migrate_customer_id"];
        $this->_routes["/vendor_rate/export_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "exportExcel"];
        $this->_routes["/vendor_rate/get_all_select_hsn"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllSelectHsns"];
    }
    private function bill_routes()
    {
        $controller = "BillController";
        // $this->_routes["/bill/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/bill/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/bill/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/bill/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/bill/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/bill/export_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "exportExcel"];
        $this->_routes["/bill/import_zip"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importZip"];
        $this->_routes["/bill/import_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importExcel"];
        $this->_routes["/bill/get_all_select"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllSelect"];
        $this->_routes["/bill/export_zip"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "exportZip"];
        $this->_routes["/bill/status_update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateStatus"];

        //  $this->_routes["/bill/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function efl_consumption_routes()
    {
        $controller = "EflConsumptionController";
        $this->_routes["/efl_consumption/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/efl_consumption/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/efl_consumption/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/efl_consumption/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/efl_consumption/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/efl_consumption/get_one_consumption_data"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneConsumptionData"];
        $this->_routes["/efl_consumption/get_all_consumption_data"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllConsumptionData"];
        $this->_routes["/efl_consumption/get_all_consumption_data_hub"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllConsumptionHubWise"];
        $this->_routes["/efl_consumption/get_one_consumption_data_hub"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneConsumptionDataHub"];

        $this->_routes["/efl_consumption/import_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importExcel"];
        $this->_routes["/efl_consumption/import_cms_excel"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "importCmsExcel"];
    }
    private function invoice_routes()
    {
        $controller = "InvoiceController";
        $this->_routes["/invoice/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/invoice/insert_manual"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insertManual"];
        $this->_routes["/invoice/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/invoice/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/invoice/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/invoice/get_one_details"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneDetails"];
        $this->_routes["/invoice/get_all_select"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllSelect"];
        $this->_routes["/invoice/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/invoice/generate_invoice"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "generate"];
        $this->_routes["/invoice/update_invoice"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/invoice/get_all_customer"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getInvoiceByCustomerID"];
        $this->_routes["/invoice/get_one_invoice"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getInvoiceByBillID"];
        $this->_routes["/invoice/download_invoice"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "downloadInvoice"];
        $this->_routes["/invoice/get_pdf"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getPdf"];
        $this->_routes["/invoice/get_sign_info"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getSingInfo"];
        $this->_routes["/invoice/verify_sign"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "checkSingInfo"];
        $this->_routes["/invoice/update_remarks"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateRemarks"];
        $this->_routes["/invoice/status_update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "updateStatus"];
        $this->_routes["/invoice/update_number"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "invoiceNumberUpdate"];
    }
    private function efl_vehicles_routes()
    {
        $controller = "EflVehiclesController";
        $this->_routes["/efl_vehicles/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/efl_vehicles/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/efl_vehicles/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/efl_vehicles/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/efl_vehicles/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/efl_vehicles/get_one_parking_data"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneParkingData"];
        $this->_routes["/efl_vehicles/get_all_parking_data"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllParkingData"];
        $this->_routes["/efl_vehicles/get_all_parking_data_hub"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllParkingHubWise"];
        $this->_routes["/efl_vehicles/get_one_parking_data_hub"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOneParkingDataHub"];

        $this->_routes["/efl_vehicles/export_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "exportExcel"];

        $this->_routes["/efl_vehicles/import_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importExcel"];
        $this->_routes["/efl_vehicles/Vehicle_Report"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "VehicleReport"];
        $this->_routes["/efl_vehicles/get_report_dashboard"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getDashBoardHubWise"];
        $this->_routes["/efl_vehicles/get_report_date"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getDashBoardHubWiseDate"];
        $this->_routes["/efl_vehicles/get_hub_capacity_report"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllHubCapacity"];
    }

    private function site_routes()
    {
        $controller = "SiteController";
        $this->_routes["/site/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/site/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/site/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/site/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/site/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
        // getOrgOnePdf
    }
    private function efl_hsns_routes()
    {
        $controller = "EfllHsnsController";
        $this->_routes["/efl_hsns/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/efl_hsns/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
    }
    private function meter_readings_routes()
    {
        $controller = "MeterReadingsController";
        $this->_routes["/meter_readings/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/meter_readings/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/meter_readings/get_all"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/meter_readings/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/meter_readings/import_excel"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "importExcel"];
    }
    private function vehicles_types_routes()
    {
        $controller = "VehiclesTypesController";
        $this->_routes["/vehicles_types/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/vehicles_types/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/vehicles_types/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/vehicles_types/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
    }
    private function payment_routes()
    {
        $controller = "PaymentController";
        $this->_routes["/payment/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/payment/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/payment/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/payment/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/payment/get_all_report"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllReport"];
        $this->_routes["/payment/get_customer_ledger"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getAllCustomerLedger"];
    }

    private function get_all_Routes()
    {
        $this->auth_routes();
        $this->users_routes();
        $this->role_routes();
        // not requiered ( user role, hub group )
        $this->user_role_routes();
        $this->hub_groups_routes();
        //
        $this->efl_office_routes();
        $this->hubs_routes();
        $this->state_db_routes();
        $this->customer_routes();
        $this->vendors_routes();
        $this->vendor_rate_routes();
        $this->bill_routes();
        $this->efl_consumption_routes();
        $this->efl_vehicles_routes();
        $this->invoice_routes();
        $this->site_routes();
        $this->efl_hsns_routes();
        $this->meter_readings_routes();
        $this->vehicles_types_routes();
        $this->payment_routes();
        return $this->_routes;
    }


    /**
     * 
     */
    static public function getRoutes()
    {
        $obj = new self();
        return $obj->get_all_routes();
    }
}
