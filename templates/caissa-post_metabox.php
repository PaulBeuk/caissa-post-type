<table>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="meta_subtitle">ondertitel</label>
        </th>
        <td>
            <input type="text" id="meta_subtitle" placeholder="ondertitel" name="meta_subtitle" size="35" value="<?php echo @get_post_meta($post->ID, 'meta_subtitle', true); ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="meta_intro">Intro</label>
        </th>
        <td>
			<textarea rows="5" cols="63" id="meta_intro" placeholder="intro" name="meta_intro"><?php echo @get_post_meta($post->ID, 'meta_intro', true); ?></textarea>
        </td>
    </tr>
</table>
