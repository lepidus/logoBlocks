# Logo Blocks — OJS 3.3

Other languages: [Português (Brasil)](docs/README.pt_BR.md) | [Español](docs/README.es.md)

The **Logo Blocks** plugin displays logos of sponsors, supporting organizations, or indexing services on the journal's public pages, in the **sidebar**, in a **strip above the footer**, or in both locations. Each logo can have a link and a short description.

For example, a journal can create an “Indexing services” block with logos and links to its database profiles, and a “Support” block with the institutions that support it. Each journal configures its own blocks, titles, images, and display order.

![Logo Blocks plugin demonstration](docs/logoBlocksDemo.gif)

## Features

- Create multiple blocks, with titles translated into the journal's languages.
- Choose from a logo gallery included with the plugin or upload your own images.
- Resize images to 150 px wide while preserving their aspect ratio, with optional border cropping when uploading images.
- Reorder, edit, hide, and delete blocks and logos.
- Configure each block's location, description display, and grayscale effect.

**The gallery is an image catalog, not an integration with indexing services.** Selecting a logo does not register the journal in a database, confirm its indexing status, or automatically retrieve its profile address. The journal's team must choose the logos and provide the corresponding links.

The plugin does not send emails, change the submission or review workflow, or create scheduled tasks. **No cron configuration is required.** Images are processed when preparing a preview or saving a logo; blocks are displayed when OJS renders public pages.

## Requirements and installation

This version is intended for **OJS 3.3**. The server requires:

- PHP with the **GD** extension and PNG, JPEG, and GIF support.
- A journal public directory that OJS can write to.

To install and enable:

1. Place the `logoBlocks` folder containing the plugin files in `plugins/blocks/` within your OJS installation. The version file must be at `plugins/blocks/logoBlocks/version.xml`.
2. Log in to OJS as a journal manager or administrator and open the journal you want to configure.
3. Under **Settings → Website → Plugins → Installed Plugins → Block Plugins**, enable **Logo Blocks**.
4. Under **Settings → Website → Appearance → Setup → Sidebar**, add **Logo Blocks** to the sidebar and save.

**Step 4 is also required to display logos only above the footer.** OJS loads this plugin when processing the sidebar. Each block's location setting determines where it appears.

Enabling the plugin does not create blocks or add logos automatically. Follow the steps below to create your first block.

## Creating the first block

1. Open **Settings** in the **Logo Blocks** plugin menu.
2. Click **Add block**.
3. Enter a **Block title**, such as “Indexing services”. Use the OJS language selector to fill in the journal's primary language and, optionally, its translations. The limit is 200 characters per language.
4. Under **Block customization**, choose where to display it. By default, new blocks appear in the sidebar, with descriptions hidden and images in color.
5. Click **Save**. In the block list, click the pencil (**Edit**) to open the new block and add its logos.
6. Add at least one logo using one of the options below and check the result on the journal's public page.

The public title follows the language selected by the visitor; if no translation is available, it uses the journal's primary language. Older titles, entered before translation support was added, are preserved in the primary language. **Blocks without logos do not appear on the public page.**

## Adding logos

### Choosing a logo from the gallery

1. Open the block for editing and click **Logo gallery**.
2. Select an image. The link and description fields appear below the gallery.
3. If desired, enter the full address of the journal's page on the indexing service, starting with `https://` or `http://`, and a description of up to 100 characters. Both are optional.
4. Click **Save**. The logo is added to the end of the list, with the name and acronym defined in the catalog. You can then use **Edit** to adjust these details.

The gallery includes DOAJ, Google Scholar, Latindex, SciELO, Scopus, and Web of Science, among others. Cards show the acronym, when available, or the name, and are sorted by full name. The list of entries is in [logos/catalog.json](logos/catalog.json).

The catalog and its images are bundled with the plugin; images are not downloaded from external services during use. All blocks share this catalog, but each addition saves a processed copy in the journal's public directory. Editing or deleting an added logo does not change the catalog or copies in other blocks or journals.

### Uploading your own image

1. Open the block for editing and click **Add logo**.
2. Enter the **Logo title** and select a PNG, JPEG, or GIF image.
3. Optionally, enter the acronym, link, and description.
4. Check the preview. To remove empty margins, select **Crop borders**.
5. Save the logo form.

Uploaded images are added only to the block being edited; they do not become part of the shared gallery.

### Field reference

| Field | Purpose |
| --- | --- |
| **Logo title** | Required name, up to 200 characters. Used as the image's alternative text and as a tooltip on hover. |
| **Acronym (optional)** | Up to 30 characters. Highlighted in the administration list next to the full name; does not replace the image's alternative text. |
| **Link (optional)** | HTTP or HTTPS address opened in a new tab when the image is clicked. Without a link, the logo is simply an image. |
| **Description (optional)** | Plain text of up to 100 characters, displayed below the image when descriptions are enabled for that location. HTML formatting is not accepted. |

The block title is multilingual. Each logo field has a single value, used in all page languages.

## Customizing the display

Click the block's pencil (**Edit**), adjust the options under **Block customization**, and click **Save**.

### Location: sidebar, footer, or both

In **Select where to display the block**, choose:

| Option | Result on the public page |
| --- | --- |
| **Show in the journal sidebar** | Logos in a list in the sidebar. |
| **Show in the journal footer** | Logos in a horizontal strip above the standard footer, without showing this block in the sidebar. |
| **Show in both locations** | The same block appears in the sidebar and above the footer. |

The strip above the footer wraps logos onto new lines when space is limited. A line separates the group of blocks from the journal's content, with no separators between blocks.

The appearance depends on the theme. The strip appears on pages that load the plugin through the sidebar and use the standard OJS 3.3 footer, or a theme that preserves the element with the `pkp_structure_footer_wrapper` class. Keep **Logo Blocks** selected in the sidebar settings even when choosing only the footer.

### Descriptions below logos

Under **Logo description display**, choose **Do not display**, **Show in the journal sidebar**, **Show in the journal footer**, or **Show in both locations**.

This option controls only the text below images; it does not change the block's location. For example, a description configured only for the footer does not appear if the block is configured only for the sidebar. Logos without a description do not display additional text.

Descriptions are hidden by default. Selecting **Do not display** preserves the saved text.

### Grayscale

Select **Enable monochrome mode, logos become colorful when interacting.** and save. The block's logos appear in grayscale in the sidebar and above the footer, returning to color when hovered over or when their links receive keyboard focus. They return to grayscale when the interaction ends.

The effect uses CSS and does not modify image files. Clear the option and save to keep colors visible at all times.

## Editing, ordering, and saving

To edit a logo, use **Edit** on its row. You can change its text and link or replace the image. **Leave the file field empty to preserve the current image.** Editing keeps the logo's position in the list; replacing the image removes the previous public file.

To reorder logos or blocks, drag the **dotted handle on the left** of the row. You can also focus the handle using the keyboard and use the up and down arrow keys. The order is saved automatically when you drop the item or move it using the keyboard, and applies to both the sidebar and the strip above the footer. New items are added to the end of the list. If saving fails, the screen restores the previous order and reports the error.

Not all actions use the same **Save** button:

| Action | When it is saved |
| --- | --- |
| Create or edit a block's title and customization | When clicking **Save** in the block form. |
| Add or edit a logo | When saving that logo's form. |
| Reorder logos or blocks | Automatically, when the order changes. |
| Enable or disable a block using its toggle | Immediately. |
| Delete a logo or block | After confirming deletion. |

Closing the block form without saving does not undo changes already saved to logos, order, or visibility.

## Hiding, disabling, or deleting: what happens?

| Action | Effect | Data preserved? |
| --- | --- | --- |
| Turn off a block's toggle | Hides that block in the sidebar and above the footer. Other blocks keep their own settings. | Yes: logos, text, order, and options. |
| Disable the plugin under **Installed Plugins** | Hides all plugin blocks in that journal. Does not change OJS submissions, reviews, or emails. | Yes: settings and logo files remain stored. |
| Remove **Logo Blocks** from the sidebar settings | Prevents loading through the sidebar, also affecting the strip above the footer. | Yes. |
| Delete a logo | Removes the logo from the block and deletes its public file. | No, for the deleted logo. |
| Delete a block using the trash icon | Removes the block, its settings, all its logos, and their public files. | No, for the deleted block. |

The toggle next to the pencil is **green when the block is enabled** and **gray when disabled**. New blocks start enabled. To display a hidden block again, turn its toggle back on; if you disabled the plugin, enable it again and check that it is selected in the sidebar settings.

**Hiding a block or disabling the plugin does not make its files private.** Images remain in the public directory and can be accessed by anyone with their addresses. To remove a logo and its file through the plugin, delete it.

## Cropping, previews, and image sizes

When uploading or replacing an image, the preview shows how it will be saved. **Crop borders** is initially unchecked: select it to check the cropped result or clear it to return to the uncropped version. Generating a preview does not save the logo; you must confirm the form.

Cropping removes fully transparent or uniformly colored outer margins, such as white or black, before resizing. For opaque backgrounds, the plugin identifies a predominant color in at least three corners, allowing for small compression variations. It preserves a one-pixel margin from the original image and the aspect ratio of the remaining content. For images with transparency, it removes only fully transparent margins, preserving white artwork.

Cropping does not remove the logo's internal background. Gradients, textures, and corners without a predominant color may prevent cropping; elements very close to the background color may be interpreted as margins. Images that are a single color or fully transparent keep their proportions.

Gallery cards already show the images: there is no cropping or additional preview. The selected image is only resized. To crop an existing logo, open **Edit** and upload the image again with **Crop borders** selected. Changing only the text or link preserves the existing image.

### Formats and limits

- **PNG, JPEG, and GIF** are accepted, up to **5 MB** and **12 million pixels** (width × height). SVG is not accepted.
- PHP's `upload_max_filesize` and `post_max_size` limits also apply. The latter must accommodate the file and the other form fields.
- Images are converted to **PNG**, preserving transparency. Animated GIFs become static images using the first frame.
- Saved files are **150 px wide**, with proportional height. Smaller images are also enlarged, which may reduce sharpness. Images whose final height exceeds **3000 px** are rejected.
- On the public page, display height is limited to **100 px**, with width reduced proportionally. A tall logo may therefore appear less than 150 px wide. The display also adapts to the available sidebar space.
- After a validation error, select the file again before saving.

## Storage and separation between journals

Settings are saved per journal in OJS plugin settings; the plugin does not create its own table. Processed files are stored in `public/journals/<id>/`, or the corresponding path defined by `public_files_dir`.

Each block keeps its own logo list and options. Operations in one journal do not change blocks in other journals. The only shared component is the image catalog bundled with the plugin files.

Management uses OJS plugin access control. Write operations require POST requests with a CSRF token.

## Troubleshooting

| Situation | What to check |
| --- | --- |
| No blocks appear | Check that the plugin is enabled and selected in the sidebar settings, that the block's toggle is on, and that it contains at least one logo. |
| The block appears in the sidebar but not above the footer | Check the location selected and saved for the block, and whether the theme uses the `pkp_structure_footer_wrapper` element. |
| The description does not appear | Enter the logo's description and enable its display in a location where the block is also visible. |
| The image cannot be saved | Check the format, size, pixel count, GD extension, PHP upload limits, and write permissions for the public directory. Select the file again after fixing the problem. |
| The logo does not open an address | Edit the logo and enter an HTTP or HTTPS link. The gallery does not fill in the address automatically. |
| The order was not saved | Reopen the settings to refresh the list and try again. |

## Updating installations using the old name

This guidance applies to installations that used the plugin's previous folder name.

The installation folder is `logoBlocks`, and the `<application>` field in `version.xml` must contain `logoBlocks`. After renaming a previous installation's folder from `sponsorsBlock` or `sponsors` to `logoBlocks`, open **Website → Plugins → Installed Plugins** so that OJS registers the product with the new path. Then reopen **Website → Appearance → Setup → Sidebar** and check the plugin selection.

The class is `LogoBlocksPlugin`, and the internal key is `logoblocksplugin`. This key matches the lowercase class name, as required by OJS 3.3 when looking up enabled plugins. The folder and product have the final name `logoBlocks`.

Settings previously saved under `sponsorsblockplugin` must be migrated to the new key to retain activation, logos, and titles. The sidebar selection must also use `logoblocksplugin`. Renaming the folder alone does not transfer this data.

## Development validation

From the root of the OJS installation, run:

```sh
php plugins/blocks/logoBlocks/tests/image.php
php plugins/blocks/logoBlocks/tests/grid.php
php plugins/blocks/logoBlocks/tests/blocks.php
php plugins/blocks/logoBlocks/tests/gallery.php
php plugins/blocks/logoBlocks/tests/preview.php
```

The tests cover image processing, previews, the gallery, and logo and block management. Grid tests use real OJS form and GridHandler classes, with requests and settings simulated in memory. They check editing, link validation, POST/CSRF, separation between journals, ordering, and file persistence or removal using prepared images. **They do not perform HTTP uploads or browser authentication and do not replace validation of the complete workflow.**

To check the workflow in an OJS 3.3 installation, create a block, add logos from the gallery and by uploading, with and without links, and check the public page. Check the sidebar and footer, descriptions, grayscale, ordering, editing, hiding, and deletion. Also check a second journal to verify that settings are separate.

## License

GNU GPL v3, as specified in OJS's `docs/COPYING`.
