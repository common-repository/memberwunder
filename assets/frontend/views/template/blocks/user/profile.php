<?php \MemberWunder\Controller\User\General::message( $status, $type, isset( $_REQUEST[ $controller_key ] ) ? htmlspecialchars( $_REQUEST[ $controller_key ] ) : '' ); ?>
<form method="post" action="">
    <input type="hidden" name="<?= $controller_key;?>" value="<?= $type;?>" />
    <?php
        foreach( $fields as $label => $group ):
            echo sprintf( '<div class="profile-headtitle"><h4>%s</h4></div>', $label );
            foreach( $group as $field ):
    ?>
            <div class="row field">
                <div class="col-sm-4 lbl">
                    <label for="<?= $field['key'];?>">
                        <?= $field['label'];?>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input 
                        id="<?= $field['key'];?>" 
                        type="<?= $field['type'];?>" 
                        name="<?= $field['key'];?>" 
                        value="<?= esc_attr( $values[ $field['key'] ] ); ?>" 
                    />
                </div>
            </div>
    <?php
            endforeach;
        endforeach;
    ?>

    <div class="row">
        <div class="col-sm-8 col-sm-offset-4">
            <button type="submit" class="but green w100">
                <?php _e( 'Save', TWM_TD ); ?>
            </button>
        </div>
    </div>
</form>
<br/>