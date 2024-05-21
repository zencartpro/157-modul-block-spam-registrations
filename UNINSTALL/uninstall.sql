#############################################################################################
# Block Spam Registrations 1.2.0 Uninstall - 2024-05-21 - webchills
# NUR AUSFÜHREN FALLS SIE DAS MODUL VOLLSTÄNDIG ENTFERNEN WOLLEN!!!
#############################################################################################
DELETE FROM configuration_group WHERE configuration_group_title = 'Block Spam Registrations';
DELETE FROM configuration WHERE configuration_key LIKE 'BLOCKSPAMREGISTRATIONS_%';
DELETE FROM configuration_language WHERE configuration_key LIKE 'BLOCKSPAMREGISTRATIONS_%';
DELETE FROM admin_pages WHERE page_key = 'configBlockSpamRegistrations';
DELETE FROM admin_pages WHERE page_key = 'customersBlockSpamRegistrations';
DROP TABLE IF EXISTS customers_spam;