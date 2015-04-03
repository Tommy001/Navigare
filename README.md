# Navigare Necesse Est

Navigare Necesse Est is a Q&A forum for boating enthusiasts. The language used is Swedish. 

As a member you can ask qustions, answer questions and make comments. It's also possible to up or down vote other user posts. As a member you will obtain points for every action made, and also based on how other users deem your posts by voting upon them.

Without membership it's only possible to search for questions and read posts.

If you want to download this site, this is how to do it:
Clone the repository with the address given in the right hand menu.

**Dependencies:** for the site to work, you need to run composer and download the Anax MVC framework, and two classes named CDatabase and CForm. Your composer.json should thus have these rows included:
>     "require": {
    "anax/mvc": "dev-master",
    "mos/cdatabase": "dev-master",
    "mos/cform": "2.*@dev"
    }

After this download you may want to make one change in CForm, which is a PHP class for creating, rendering and validating HTML forms, in order to style the form for adding or editing tags for a question. Simply replace the code on row 278 (begins with '$ret .= ...') in the file *vendor/mos/cform/HTMLForm/CFormElement.php* with the code found in the file *formstyle.php* on the root level of the installation folder for this Q&A forum.

The site will work fine anyway, but this particular form layout will be more appealing if you make this change.

To connect to your mysql database you will need to add your password, host name and so forth in the file *Navigare/app/config/database_mysql.php*.

The mysql database tables needed is supplied in a single file named *Navigare/setup_navigare.sql*. Change the **USE** statement on the first row to match your database name. You can use phpMyAdmin to import the tables to your mysql database. The setup is complete with a sample content. Simply remove the *INSERT* statements to make a clean installation without content.

It is also possible to intialize the database tables by adding "/setup" to the start page URL in the address field of you web browswer. This will drop and add these same tables with content to the database.

A sample installation of the site is available [here](http://www.student.bth.se/~toja14/phpmvc/kmom07/projekt/webroot/). 


