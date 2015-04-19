# Baun Categories Plugin

A categories plugin for BaunCMS

## Installation Instructions

### Using Composer

Some of the features in this plug-in rely on my development branch of the Baun CMS Framework. I have created pull 
requests, but in the meantime please change `composer.json` in your Baun application to contain the following 
repositories:

    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/maccath/baun-categories"
      },
      {
        "type": "vcs",
        "url": "https://github.com/maccath/Framework"
      }
    ],
    
And alter the `require` portion of `composer.json` to contain the following lines:

    "require": {
        "maccath/baun-categories": "dev-master",
        "bauncms/framework": "dev-develop"
    },
    
I will update these installation instructions once the applicable changes make it back into the Baun CMS Framework.

### Configuration

Add the following line to your `config/plugins.php` file:

    'BaunPlugin\Categories\Categories',

Run the following command inside your project directory:

    php baun publish:config maccath/baun-categories

This will create config files inside the `config/plugins/maccath/baun-categories` directory, which you may then edit to 
your liking.

The following configuration options are available:

 * `title` the title used on the categories list page and in the navigation
 * `category_url` the path used to access your blog categories, no leading slash
 * `exclude_from_nav` (boolean) whether or not to exclude the categories page from the navigation

## Usage Instructions

### Adding categories to posts

To add categories to your posts, simply add `categories: categoryOne, categoryTwo` to the top of your blog post files. 
For example, the following file will create a post with the categories 'volutpat' and 'consectetur':

    title: Post 1
    categories: consectetur, volutpat
    ----
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ut vehicula erat. Morbi volutpat posuere auctor. 
    Vivamus condimentum, purus nec tempus euismod, enim massa blandit est, vel elementum lacus nulla ut sem. Integer 
    orci libero, rutrum id nisl et, euismod auctor augue.

### Viewing all posts in a category

By default, posts for categories will appear at the following path: `/categories/categoryName`. The path used to display
categories can be configured in the configuration file found at `config/plugins/maccath/baun-categories/categories.php`

### Editing the categories page template

Inside your themes directory (typically `public/themes/themeName/`), create `categories.html`. This file can be 
customised to display a list of categories however you like. The following example is the default file contents, which
simply displays an unordered list of categories:

    {% extends "layout.html" %}
    
    {% block content %}
    <h1>{{ categories_title }}</h1>
    <ul>
        {% for category in categories %}
        <li><a href="{{ category.link }}">{{ category.name }}</a></li>
        {% endfor %}
    </ul>
    {% endblock %}
