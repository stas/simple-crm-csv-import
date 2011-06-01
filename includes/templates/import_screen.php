<form enctype="multipart/form-data" action="" method="post">
    <div class="postbox">
        <h3 class="hndle" ><?php _e( 'Import a <tt>.csv</tt> users file','scrm_csv' )?></h3>
        <div class="inside">
            <?php wp_nonce_field( 'scrm_csv', 'scrm_csv_nonce' ); ?>
            <?php if( $results ) : ?>
                <div class="updated">
                    <p>
                        <?php echo $results[0]; ?>
                        <a href="<?php echo admin_url( 'users.php' ); ?>"><?php echo $results[1]; ?></a>
                    </p>
                </div>
            <?php endif; ?>
            <p>
                <?php _e( 'First, please upload the file.','scrm_csv' ); ?>
                <br/>
                <input type="file" name="scrm_csv_import_filename" />
                <input type="submit" name="scrm_csv_import" class="button-primary" value="<?php _e( 'Import' )?>"/>
            </p>
        </div>
    </div>
</form>