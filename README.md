Webtise_MediaStorageSync
===============

Magento media storage sync PHP shell script, helpful if you have a timeout confiured on your server and need to sync a large DB of media to file based storage.

Usage Instructions
------------------
<pre>
php mediaStorageSync.php -- help
</pre>

Options:

`--storage` Define storage you want to sync TO. Accepts db, database, file, files

`--connection` Specify connection 'default_setup' is used if not passed
