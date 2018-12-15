# REDCapToWordPress WordPress plugin

## Description
This plugin is designed to embed RedCap surveys into the WordPress site of the study. The forms 
take on the themes of the site, allowing you to embed surveys into your site while keeping the look and feel of the survey and harnessing the usefulness of RedCap.

### Pre-requisite plugins:
    
This plugin requires the [Native PHP Sessions for WordPress plugin](https://wordpress.org/plugins/wp-native-php-sessions/).
Be sure to install this before installing Dog Precision Medicine.

## Setting up the Plugin
Download the Dog Precision Medicine repository.

You'll have to manually configure a few of the variables for the plugin.

In the **example_config.ini** file, add your REDCap API token. Change the name of the 
file to config.ini.
 
Once those changes are made, add the Dog Precision Medicine folder to the plugins
folder in WordPress. You can either directly upload the folder to your hosting server to 
/htdocs/wp_content/plugins, or you can zip the repository and upload it through the admin backend
view of WordPress.

### Creating survey pages

You can either create a new page for the surveys or you can embed the surveys into an existing page.
Just add the shortcode **[screening_form]** to the page on which you want people taking the survey.


### What is the plugin doing.

The plugin is allowing study participants to fill out RedCap surveys without redirecting people to the RedCap survey site, but instead, embedding the survey into the WordPress study site. The plugin uses the PHP Sessions to keep track of participant progress. While the plugin is specifically designed for the Dog Aging Project, it could be easily tailored to any RedCap survey/records format. The code is designed to use the RedCap metadata to build the WordPress compatible forms so that any RedCap record that sends metadata to the plugin should be embedded. All edge cases have not been tested. Be sure to test your forms with the plugin before going into prodution.


This should cover it. Feel free to email me with questions: trberg@uw.edu







