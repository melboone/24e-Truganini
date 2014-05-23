<?php

// if the from is loaded from WordPress form loader plugin, 
// the phpfmg_display_form() will be called by the loader 
if( !defined('FormmailMakerFormLoader') ){
    # This block must be placed at the very top of page.
    # --------------------------------------------------
    require_once( dirname(__FILE__).'/form.lib.php' );
    phpfmg_display_form();
    # --------------------------------------------------
};


function phpfmg_form( $sErr = false ){
        $style=" class='form_text' ";

?>


<div id='frmFormMailContainer'>

<form name="frmFormMail" id="frmFormMail" target="submitToFrame" action='<?php echo PHPFMG_ADMIN_URL . '' ; ?>' method='post' enctype='multipart/form-data' onsubmit='return fmgHandler.onSubmit(this);'>

<input type='hidden' name='formmail_submit' value='Y'>
<input type='hidden' name='mod' value='ajax'>
<input type='hidden' name='func' value='submit'>
            
<div class="form-left">
    <ol class='phpfmg_form' >

            <div class="field-divider"></div>

        <li class='field_block' id='field_0_div'>
            <div class='col_field'>

            <input type="text" placeholder="Name*" name="field_0"  id="field_0" value="<?php  phpfmg_hsc("field_0", ""); ?>" class='text_box'>
            <div id='field_0_tip' class='instruction'></div>
            </div>
        </li>

            <div class="field-divider"></div>

        <li class='field_block' id='field_1_div'>
            <div class='col_field'>
            <input type="text" placeholder="email*" name="field_1"  id="field_1" value="<?php  phpfmg_hsc("field_1", ""); ?>" class='text_box'>
            <div id='field_1_tip' class='instruction'></div>
            </div>
        </li>

            <div class="field-divider"></div>

        <li class='field_block' id='field_2_div'>
            <div class='col_field'>
            <input type="text" placeholder="telephone*" name="field_2" id="field_2" value="<?php  phpfmg_hsc("field_2", ""); ?>" class='text_box'>
            <div id='field_2_tip' class='instruction'></div>
            </div>
        </li>

            <div class="field-divider"></div>

        <li class='field_block' id='field_3_div'>
            <div class='col_field'>
            <input type="text" placeholder="postcode*" name="field_3"  id="field_3" value="<?php  phpfmg_hsc("field_3", ""); ?>" class='text_box'>
            <div id='field_3_tip' class='instruction'></div>
            </div>
        </li>

            <div class="field-divider"></div>

    </ol>
</div>

<div class="form-right">
    <ol class='phpfmg_form' >
        <div class='col_field'>
        <?php phpfmg_dropdown( 'field_4', "<option disabled selected>PRICE POINT</option>|<option>$499,000 - $599,000</option>|<option>$600,000 - $699,000</option>|<option>$700,000 +</option>" );?>
        <div id='field_4_tip' class='instruction'></div>
        </div>

        <div class='col_field'>
        <?php phpfmg_dropdown( 'field_5', "<option disabled selected>HOW DID YOU HEAR ABOUT US?</option>|<option>DIRECT MAIL</option>|<option>EMAIL NOTICE</option>|<option>ONLINE</option>|<option>REFERRAL</option>|<option>SIGNAGE</option>|<option>OTHER</option>", false );?>
        <div id='field_5_tip' class='instruction'></div>
        </div>
    <p>*Mandatory fields</p>
    </ol>
            <div class="submit-button">
            <input type='submit' value='Submit' class='form_button'>
        </div>
</div>

            <div class='form_submit_block col_field'>
    

                <div id='err_required' class="form_error" style='display:none;'>
                    <label class='form_error_title'>Please check the required fields</label>
                </div>

                <span id='phpfmg_processing' style='display:none;'>
                    <img id='phpfmg_processing_gif' src='<?php echo PHPFMG_ADMIN_URL . '?mod=image&amp;func=processing' ;?>' border=0 alt='Processing...'> <label id='phpfmg_processing_dots'></label>
                </span>
            </div>
          
</form>

<iframe name="submitToFrame" id="submitToFrame" src="javascript:false" style="position:absolute;top:-10000px;left:-10000px;" /></iframe>

</div> 
<!-- end of form container -->


<!-- [Your confirmation message goes here] -->
<div id='thank_you_msg' style='display:none;'>
<p>Your form has been sent. We will contact you soon.</p>
<p>Thank you!</p>
</div>

<?php
            
    phpfmg_javascript($sErr);

} 
# end of form

function phpfmg_form_css(){
    $formOnly = isset($GLOBALS['formOnly']) && true === $GLOBALS['formOnly'];
?>

<?php
}
# end of css
 
# By: formmail-maker.com
?>