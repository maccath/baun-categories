# Baun Categories Plugin

A categories plugin for BaunCMS

## Installation Instructions

### Using Composer

Add the following lines to your composer.json file:

    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/maccath/baun-categories"
      }
    ],

### Configuration

Run the following command inside your project directory:

    php baun publish:config maccath/baun-categories

This will create config files inside the `config/plugins/maccath/baun-categories` directory, which you may then edit to 
your liking.

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
