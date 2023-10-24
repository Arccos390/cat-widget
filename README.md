# Cat Widget Module for Drupal

The `cat_widget` module provides two blocks, allowing users to search for details about various cat breeds. It fetches data from an external API to display cat breed details and images.

## Features:

- **Cat Search Widget**: A widget where users can select a cat breed and view details and images related to the selected breed.
- **Cat Of The Day Block**: A block that shows a different image of a cat each day. You can manually run the cron job to download a new cat image. Users can like or dislike the image.
- **Media Entity Integration**: A media entity of type `cat_widget_image` is created to manage the images associated with the cat breeds.
- **Cat Report Page**: A view page where the admin user can check all the cat images with their like/dislike numbers. It can be accessed on this page `/admin/cat-report`.

## Dependencies:

- Ensure you have enable the **media** and **ultimate_cron** modules.

## Installation:

1. Download and place the `cat_widget` module in your Drupal installation's `modules/custom/` directory.
2. Navigate to `Extend` in the Drupal admin menu and enable the `Cat Widget` module.

## Usage:

1. After installation, place the `Cat breed search` and `Cat of the day` blocks in a desired region via the Drupal block layout.
2. Users can then use the form to select a cat breed.
3. After selecting a breed and clicking on `Search`, the details of the breed, along with related images, will be displayed below the form.

## Uninstallation:

1. When you uninstall the module, all media entities of type `cat_widget_image` will be automatically deleted.

## Future improvements:
1. Only a registered user can like or dislike an image, and only once per image.
2. Create a `drush` script to take seven more images and store them in `daily_cat_images/backup`. If the API does not work for a day, we will pick an image from the backup directory as the new cat of the day! An admin can run the script anytime to fill up the backup directory.
3. Better theming for the blocks. Now, the images have their width and height that are applied. Create an image style to work with that.
4. **Fun AI project**: Find the breed of the `Cat Of The Day` image, if not known. We will use image classification, which is a type of supervised learning.
