begin;
create table del_users as select id from users where user_group_id in (select id from groups where subclass <> 'ADMIN_GROUP');
update users set registered_by_id = null where registered_by_id in (select id from del_users);
update transactions set transfer_id = null, original_transfer_id = null, last_occurrence_success_id = null, last_occurrence_failure_id = null, feedback_id = null, access_client_id = null;
delete from amount_reservations;
delete from vouchers;
delete from voucher_packs;
delete from transfer_status_logs;
delete from transfers_transfer_status_flows;
delete from failed_payment_occurrences;
delete from transfers;
delete from transaction_enum_values;
delete from stored_files where transaction_value_id is not null;
delete from transaction_custom_field_values;
delete from scheduled_payment_installments;
delete from transaction_authorizations;
delete from refs;
delete from transactions;
delete from access_client_logs where access_client_id in (select id from access_clients where user_id in (select id from del_users));
delete from access_clients where user_id in (select id from del_users);
delete from user_account_fee_logs;
delete from closed_account_balances where account_id in (select id from accounts where subclass='USER');
delete from account_limit_logs where account_id in (select id from accounts where subclass='USER');
update accounts set account_rates_id = null where subclass='USER';
delete from account_rates where account_id in (select id from accounts where subclass='USER');
delete from account_balance_counters where account_id in (select id from accounts where subclass='USER');
delete from accounts where subclass='USER';
delete from webshop_ads_delivery_methods;
delete from ad_delivery_methods;
delete from ad_history_logs;
delete from ads_categories;
delete from ad_order_products;
delete from notified_ad_interests;
delete from ads_addresses;
update ads set image_id = null;
delete from stored_files where ad_id is not null;
delete from ad_enum_values;
delete from ad_custom_field_values;
delete from ad_questions;
delete from ad_order_logs;
delete from stored_files where contact_info_id is not null;
delete from contact_infos;
update addresses set ad_order_id =null;
delete from ad_orders;
delete from ad_web_shop_settings;
delete from ad_interests;
delete from addresses;
delete from ads;
delete from phones;
delete from entity_property_logs;
delete from entity_logs;
update users set image_id = null;
delete from stored_files where user_id is not null;
delete from agreement_logs;
delete from passwords where user_id in (select id from del_users);
delete from brokering_logs;
delete from brokerings;
delete from login_history_logs where user_id in (select id from del_users);
delete from bulk_actions_users;
delete from user_enum_values;
delete from stored_files where user_value_id is not null;
delete from user_custom_field_values;
delete from notification_type_settings where subclass = 'USER';
delete from notification_settings where subclass = 'USER';
delete from notifications where user_id in (select id from del_users);
delete from operator_group_logs;
delete from operator_groups_restrict_payments_users;
delete from operator_groups_custom_operations;
delete from operator_groups_record_types;
delete from operator_groups_account_types;
delete from operator_groups_payment_types;
update users set operator_group_id = null;
delete from operator_groups;
delete from outbound_sms;
delete from inbound_sms;
delete from stored_files where record_value_id is not null;
delete from record_enum_values where owner_id in (select id from record_custom_field_values where owner_id in (select id from records where user_id in (select id from del_users)));
delete from record_custom_field_values where owner_id in (select id from records where user_id in (select id from del_users));
delete from records where user_id in (select id from del_users);
delete from user_group_logs where user_id in (select id from del_users);
update user_status_logs set by_id = null where by_id in (select id from del_users);
delete from user_status_logs where user_id in (select id from del_users);
update users set operator_user_id = null;
delete from user_activities where user_id in (select id from del_users);
delete from user_regional_settings where user_id in (select id from del_users);
delete from contacts where owner_id in (select id from del_users);
delete from contacts where contact_id in (select id from del_users);
delete from alerts where user_id in (select id from del_users);
delete from failed_action_logs where user_id in (select id from del_users);
delete from tokens where user_id in (select id from del_users);
delete from user_channels where user_id in (select id from del_users);
delete from users_products_logs where user_id in (select id from del_users);
delete from users_products where user_id in (select id from del_users);
delete from messages_to_groups;
delete from messages_to_users;
delete from messages;
delete from mailing_lists_to_groups;
delete from mailing_lists_to_users;
delete from mailings;
delete from mailing_lists;
delete from notifications where related_user_id is not null;
delete from reference_history;
delete from users_dashboard_actions;
delete from documents where user_id in (select id from del_users);
delete from error_logs;
delete from users_ignore_feedbacks;
delete from user_registration_account_configuration where user_id in (select id from del_users);
delete from users where id in (select id from del_users);
drop table del_users;
commit;
