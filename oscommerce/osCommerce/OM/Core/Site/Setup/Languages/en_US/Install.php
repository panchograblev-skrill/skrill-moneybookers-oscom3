# osCommerce Online Merchant
#
# @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
# @license BSD License; http://www.oscommerce.com/bsdlicense.txt

page_title_installation = New Installation

page_heading_web_server = Web Server
page_heading_database_server = Database Server
page_heading_store_settings = Online Store Settings
page_heading_finished = Finished!

text_installation = This web-based installation procedure will setup and configure osCommerce Online Merchant to correctly run on this server.<br /><br />Please following the on-screen instructions that will take you through the web server, database server, and online store configuration options.
text_successful_installation = The installation and configuration was successful!
text_go_to_shop_after_cfg_file_is_saved = Please visit your store after the configuration file has been saved:

param_web_address = WWW Address
param_web_address_description = The web address to the online store.

param_database_server = Database Server
param_database_server_description = The address of the database server in the form of a hostname or IP address.
param_database_username = Username
param_database_username_description = The username used to connect to the database server.
param_database_password = Password
param_database_password_description = The password that is used together with the username to connect to the database server.
param_database_name = Database Name
param_database_name_description = The name of the database to hold the data in.
param_database_port = Database Server Port
param_database_port_description = The database server port number to connect to. (If unsure please leave this value empty)
param_database_type = Database Type
param_database_type_description = The database server software that is used.
param_database_prefix = Database Table Prefix
param_database_prefix_description = The prefix to use for the database tables.

param_store_name = Store Name
param_store_name_description = The name of the online store that is presented to the public.
param_store_owner_name = Store Owner Name
param_store_owner_name_description = The name of the store owner that is presented to the public.
param_store_owner_email_address = Store Owner E-Mail Address
param_store_owner_email_address_description = The e-mail address of the store owner that is presented to the public.
param_administrator_username = Administrator Username
param_administrator_username_description = The administrator username to use for the administration tool.
param_administrator_password = Administrator Password
param_administrator_password_description = The password to use for the administrator account.

param_time_zone = Time Zone
param_time_zone_description = The time zone to use for dates.

param_database_import_sample_data = Import Sample Data
param_database_import_sample_data_description = Inserting sample data into the database is recommended for first time installations.

box_steps_step_1 = Web/Database Server
box_steps_step_2 = Online Store Settings
box_steps_step_3 = Finished!

box_info_step_1_title = Step 1: Web/Database Server
box_info_step_1_text = The web server is responsible for serving the pages of the online store to visitors and customers.<br /><br />The database server stores the content of the online store such as product information, customer information, and orders that have been made.<br /><br />Please consult your server administrator if your database server parameters are not known.

box_info_step_2_title = Step 2: Online Store Settings
box_info_step_2_text = Please enter the name of your online store and the contact information for the store owner.<br /><br />The administrator username and password are used to log into the Administration Tool.

box_info_step_3_title = Step 3: Finished!
box_info_step_3_text = Congratulations on installing and configuring your new osCommerce Online Merchant store!<br /><br />We wish you all the best with the business of your online store and look forward to seeing you in our community!<br /><br />- The osCommerce Team

error_configuration_file_not_writeable = The webserver was not able to write the installation parameters to its configuration file due to file permission problems.<br /><br />Please verify the file permissions of the configuration file and try again by clicking on the Retry button below.<br /><br />The configuration file is located at:<br /><br />%s
error_configuration_file_alternate_method = Alternatively the contents of the textbox below can be saved to the configuration file by hand.

rpc_database_connection_test = Testing database connection..
rpc_database_connection_error = There was a problem connecting to the database server. The following error had occured:</p><p><b>%s</b></p><p>Please verify the connection parameters and try again.
rpc_database_connected = Successfully connected to the database.
rpc_database_importing = The database structure is now being imported. Please be patient during this procedure.
rpc_database_imported = Database imported successfully.
rpc_database_import_error = There was a problem importing the database. The following error had occured:</p><p><b>%s</b></p><p>Please verify the connection parameters and try again.

rpc_database_store_configuration = The store settings are now being saved to the database. Please be patient during this procedure.
rpc_database_store_configuration_error = There was a problem saving the store settings to the database. The following error had occured:</p><p><b>%s</b></p><p>Please verify the store settings and try again.

rpc_database_sample_data_importing = The sample data is now being imported into the database. Please be patient during this procedure.
rpc_database_sample_data_imported = Database sample data imported successfully.
rpc_database_sample_data_import_error = There was a problem importing the database sample data. The following error had occured:</p><p><b>%s</b></p><p>Please verify the database server and try again.
