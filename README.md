# highloadblockrights
This module provides a convenient way to manage permissions for elements in Bitrix highloadblocks. It is built using the Bitrix D7 framework, which ensures compatibility and extensibility.

Features:
- Grant and revoke permissions for individual users and user groups on highloadblock elements.
- Define custom permission levels with different access rights, such as read, write, delete, etc.
- Access control similar to the permissions management in Bitrix infoblock elements.
- Easy-to-use interface for managing permissions through the Bitrix administration panel.

Requirements:
- Bitrix CMS versions greater than 18.5.180.
- Highloadblock module installed and configured.

Installation:
1. Download the module files from the GitHub repository.
2 .Copy the module files to the Bitrix modules directory.
3. Install the module through the Bitrix administration panel.
4. Configure the module settings, including highloadblock selection and permission levels.

Usage:

1. Access the module's administration section through the Bitrix administration panel.
2. Select the desired highloadblock for which you want to manage permissions.
3. Define custom permission levels or use the default ones.
4. Assign permissions to individual users or user groups for each highloadblock element.
5. Save the changes, and the permissions will take effect immediately.

-Include the module in your project using the following code:
```
\Bitrix\Main\Loader::includeModule('disterveg.highloadblockrights');
```

-Create an instance of the HlblockRights class by providing the highloadblock identifier, table name, or hlblock ID as the constructor argument
```
$hlrights = new \Disterveg\HighloadblockRights\Helpers\HlblockRights('Expert');
```

-Use the getItemsFilteredByPermissions method to retrieve highloadblock elements filtered based on permissions. Pass an array of select fields as the first argument to specify the fields you want to retrieve, and specify the desired permission level as the second argument (e.g., 'read' or 'write'). You can also specify a third argument for a different user ID if needed. For example:
```
$items = $hlrights->getItemsFilteredByPermissions(['select' => ['UF_NAME', 'ID']], 'read');
```

Note: This module enhances the functionality of highloadblocks by providing granular control over element permissions. It simplifies the process of managing access rights for various user roles, improving the overall security and flexibility of your Bitrix-based applications.