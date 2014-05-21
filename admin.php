<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "email@julian.id.au" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "9b220d" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'508F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUNDkMQCGhhDGB0dHRhQxFhbWRsCUcQCA0QaHRHqwE4KmzZtZVboytAsZPe1oqiDi7mimRfQimmHyBRMt7AGgN2Mat4AhR8VIRb3AQCnJ8kjWUdfywAAAABJRU5ErkJggg==',
			'7E06' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBZtFWlgCGUICEATY3R0dBBAFpsi0sDaEOiA4r6oqWFLV0WmZiG5j9EBrA7FPNYGiF4RJDGRBogdyGIBDZhuCWjA4uYBCj8qQizuAwBxS8rv3/uSuQAAAABJRU5ErkJggg==',
			'73F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDA6Y6IIu2irSyNjAEBKCIMTS6NjA6iCCLTWEAqWsQQXZf1KqwpaEgCuE+RgewukZkO0B8V5CpSGIiELEpyGIBDRC3oIoB3dzAGBoyCMKPihCL+wA4CstuRTpI4QAAAABJRU5ErkJggg==',
			'9ED6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDoIYBFDdt+0qVPDlq6KTM1Cch+rK1gdinkMUL0iSGICWMSwuQWbmwcq/KgIsbgPAOqjy/D4oeSLAAAAAElFTkSuQmCC',
			'B1CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHUMdkMQCpjAGMDoEOgQgi7WyBrA2CDqIoKhjAIoxwsTATgqNWhW1dNXKrGlI7kNTBzUPlximHehuCQ1gDUV380CFHxUhFvcBAKz6yivik4h6AAAAAElFTkSuQmCC',
			'A595' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2Quw2AMAwF7SIbmH2cgt5IcUE2YAtTZAMYgQKmJKUjKEGKX3fy52S4HmXQU37xQx4UFFUcC0KGMbLvo40s2NQwKZQqG9n55WM/zmXO2flJgZWTGLlZ1cqsZXXfGuuNloWCkUUahgkUdu7gfx/mxe8GfOvMIayheRoAAAAASUVORK5CYII=',
			'72F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIu2srayNjAEBKCIiTS6NjA6iCCLTWEAisHVQdwUtWrp0tBVU7OQ3MfowDAF3TwQnxXNPBEgH10sAKgSXW9Ag2go0F5UNw9Q+FERYnEfAA05y0czyErcAAAAAElFTkSuQmCC',
			'E93F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUNDkMQCGlhbWRsdHRhQxEQaHRoCMcUQ6sBOCo1aujRr6srQLCT3BTQwBjpgmMeAxTwWLGKYboG6GUVsoMKPihCL+wAqaMwJSaHmigAAAABJRU5ErkJggg==',
			'D392' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaY6IIkFTBFpZXR0CAhAFmtlaHRtCHQQQRVrZW0IaBBBcl/U0lVhKzOjVkUhuQ+kjiEkoNEBzTwHEIkm5gi0nQGLWzDdzBgaMgjCj4oQi/sAdDXN9c4OAzQAAAAASUVORK5CYII=',
			'D22B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QsRGAIAxFk4INcJ9Y0Ic707iBTgEFG2QFCplSSkBLPc3v3iX/3gXKZQL8Ka/4CeMCgkINYzUJ55m4ZclGFzzZjkGkyrjxW3PJ5dhkb/zqnkLCoQ8YFIc+JOCBqQlI/a3wJE585/zV/x7Mjd8JjPrMV3DYa1YAAAAASUVORK5CYII=',
			'89EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUMDkMREprC2sjYwOiCrC2gVaXRFExOZgiIGdtLSqKVLU0NXhmYhuU9kCmMgut6AVgYM8wJaWbDYgekWbG4eqPCjIsTiPgBb3cnpPIoD+wAAAABJRU5ErkJggg==',
			'0906' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLo6OjoIIAkFtAq0ujaEOiA7L6opUuXpq6KTM1Ccl9AK2MgUB2KeQGtDGC9Iih2sIDtECHgFmxuHqjwoyLE4j4AIx/LYv11djoAAAAASUVORK5CYII=',
			'37B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RANEQ11DGaY6IIkFTGFodG10CAhAVtkKFGsIdBBAFpvC0Mra6OiA7L6VUaumLQ1dmZqF7L4pDAFAdWjmMTqwAs0TQRFjbUAXC5gi0sCK5hbRAKAYmpsHKvyoCLG4DwBucMxKCOF3hAAAAABJRU5ErkJggg==',
			'BE8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGUMdkMQCpog0MDo6OgQgi7WKNLA2BDqIYFEnguS+0KipYatCV2ZNQ3Ifmjrc5uGxA9kt2Nw8UOFHRYjFfQDUrMvuToAXrQAAAABJRU5ErkJggg==',
			'BBE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUNDkMQCpoi0sgJpEWSxVpFGV3QxqLoAJPeFRk0NWxq6amUWkvug6loZMM2bgkUsgAHDDkYHLG5GERuo8KMixOI+AI6ozQ5DwolxAAAAAElFTkSuQmCC',
			'C603' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQximMIQ6IImJtLK2MoQyOgQgiQU0ijQyOjo0iCCLAXmsQDIAyX1Rq6aFLV0VtTQLyX0BDaKtSOpgehtdwSag2uGIZgc2t2Bz80CFHxUhFvcBAC5DzRHV7bHnAAAAAElFTkSuQmCC',
			'7749' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHaY6IIu2MjQ6tDoEBKCLTXV0EEEWmwIUDYSLQdwUtWraysysqDAk9zE6MASwAnUj62UFirKGBjQgi4kARYG2oNgBVtGI6haoGKqbByj8qAixuA8AyGPMsMy0NRsAAAAASUVORK5CYII=',
			'5559' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHaY6IIkFNIg0sDYwBARgiDE6iCCJBQaIhLBOhYuBnRQ2berSpZlZUWHI7mtlaHRoCJiKrBcq1oAsFtAq0ujaEIBih8gU1lZGRwcUt7AGMIYwhDKguHmgwo+KEIv7AE8ezD56QMPeAAAAAElFTkSuQmCC',
			'DB04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQximMDQEIIkFTBFpZQhlaEQRaxVpdHR0aEUTa2UFqg5Acl/U0qlhS1dFRUUhuQ+iLtAB3TzXhsDQEEw7sLkFRQybmwcq/KgIsbgPALplz9xEY/E4AAAAAElFTkSuQmCC',
			'F68D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkMQCGlhbGR0dHQJQxEQaWRsCHURQxRpA6kSQ3BcaNS1sVejKrGlI7gtoEG1FUgc3zxXTPCxi2NyC6eaBCj8qQizuAwDVPMwLI7zj/gAAAABJRU5ErkJggg==',
			'6872' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZWgICAhAEgtoEWl0aAh0EEEWawCqA4qKILkvMmpl2Kqlq1ZFIbkvBGTeFJBKJL2tQPMCGFoZ0MQcHYAq0dzC2sAQgOHmBsbQkEEQflSEWNwHAGcszOc7PmOLAAAAAElFTkSuQmCC',
			'CDDA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGVqRxURaRVpZGx2mOiCJBTSKNLo2BAQEIIs1gMQCHUSQ3Be1atrK1FWRWdOQ3IemDlksNATDDlR1ELc4oohB3MyIIjZQ4UdFiMV9AGgkzc/WAlCBAAAAAElFTkSuQmCC',
			'B0EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUNDkMQCpjCGsDYwOiCrC2hlbcUQmyLS6IoQAzspNGraytTQlaFZSO5DUwc1D5sYNjsw3QJ1M4rYQIUfFSEW9wEAvVTKGqVTe7wAAAAASUVORK5CYII=',
			'25F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA1qRxUSmiDSwNjBMRRYLaAWLhaLobhUJAYrB9ELcNG3q0qWhq5aiuC+AodEVoQ4MGR0wxVgbRDDEgLa2sqKJhYYyguwNDRgE4UdFiMV9AL3oyxIwpqusAAAAAElFTkSuQmCC',
			'52CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHUMdkMQCGlhbGR0CHQJQxEQaXRsEHUSQxAIDGIBijDB1YCeFTVu1dOmqlaFZyO5rZZjCilAHEwsAiSGbFwC0lRXNDhGwTlS3sAaIhjqguXmgwo+KEIv7AERHy0CdDS2oAAAAAElFTkSuQmCC',
			'037A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA1qRxVgDRID8gKkOSGIiUxgaHRoCAgKQxIC6WhkaHR1EkNwXtXRV2KqlK7OmIbkPrG4KI0wdTKzRIYAxNATNDkcHVHUgt7A2oIqB3YwmNlDhR0WIxX0AIB3K9eFL6zoAAAAASUVORK5CYII=',
			'29CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHVqRxUSmsLYyOgRMdUASC2gVaXRtEAgIQNYNFmN0EEF237SlS1NXrcyahuy+AMZAJHVgyOjAANIbGoLslgYWoJggijqRBpBbAlHEQkNBbnZEERuo8KMixOI+AAOsywrVZVpeAAAAAElFTkSuQmCC',
			'DA1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIaGIIkFTGEMYQhhdEBWF9DK2sqIISbS6DAFLgZ2UtTSaSuzpq0MzUJyH5o6qJhoKKYYFnVTMMVCA0QaHUMdUcQGKvyoCLG4DwAlF8uXOh2s+AAAAABJRU5ErkJggg==',
			'574A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHVqRxQIaGIAiDlMd0MWmOgQEIIkFBjC0MgQ6OogguS9s2qppKzMzs6Yhu6+VIYC1Ea4OKsbowBoaGBqCbEcrK9AWVHUiU0QwxFgDMMUGKvyoCLG4DwA3ssyTgcB0+wAAAABJRU5ErkJggg==',
			'9D87' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUNDkMREpoi0Mjo6NIggiQW0ijS6NgRgiDkC1QUguW/a1Gkrs0JXrcxCch+rK1hdK4rNEPOmIIsJQMQCGDDc4uiAxc0oYgMVflSEWNwHAHxZzCTCl8ZiAAAAAElFTkSuQmCC',
			'6FA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmSLSwBDKEBCAJBbQItLA6OjoIIIs1iDSwNoQAFMHdlJk1NSwpauipmYhuS9kCoo6iN5WoFhoIKp5ILEGVDERLHpZA8BiKG4eqPCjIsTiPgB9K81ydvSu/QAAAABJRU5ErkJggg==',
			'FB3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGaYGIIkFNIi0sjY6BIigijU6NAQ6sKCpY2h0dEB2X2jU1LBVU1dmIbsPTR2KedjE0O3AdAummwcq/KgIsbgPABmGzcnLadVZAAAAAElFTkSuQmCC',
			'1B1C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQximMEwNQBJjdRBpZQhhCBBBEhN1EGl0DGF0YEHRC1Q3hdEB2X0rs6aGrZq2MgvZfWjqYGKNDjjEMO1Ac0uIaAhjqAOKmwcq/KgIsbgPAOU5yBr4Q+cYAAAAAElFTkSuQmCC',
			'D32C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaYGIIkFTBFpZXR0CBBBFmtlaHRtCHRgQRVrZQCKIbsvaumqsFUrM7OQ3QdW18rowIBmnsMULGIBjKh2gNziwIDiFpCbWUMDUNw8UOFHRYjFfQBMacw1AfT1kgAAAABJRU5ErkJggg==',
			'DD1B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQximMIY6IIkFTBFpZQhhdAhAFmsVaXQEiomgiTlMgasDOylq6bSVWdNWhmYhuQ9NHYoYNvNE0N2CphfkZsZQRxQ3D1T4URFicR8Au1TNf3RAUvEAAAAASUVORK5CYII=',
			'82F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0MDkMREprC2sjYwOiCrC2gVaXRFExOZwgASc3VAct/SqFVLl4aujIpCch9Q3RRWEI1iHkMAphijA8heERQ7WBuA6gKQ3ccaIBrq2sAw1WEQhB8VIRb3AQCIjMr0seCnkgAAAABJRU5ErkJggg==',
			'0C37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ0EwBEmMNYC10bXRoUEESUxkikiDQ0MAilhAK5DXCBJFuC9q6bRVq6auWpmF5D6oulYGdL0NAVMYMO0IYMBwi6MDFjejiA1U+FERYnEfAEX4zNQAcNbvAAAAAElFTkSuQmCC',
			'5AB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUIdkMQCGhhDWBsdHQJQxFhbWYGkCJJYYIBIo2ujQ0MAkvvCpk1bmRq6amkWsvtaUdRBxURDXdHMCwCpQxMTmQLSi+oWVpC9aG4eqPCjIsTiPgAGW86kNRo/PAAAAABJRU5ErkJggg==',
			'0797' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUNDkMRYAxgaHR0dGkSQxESmMDS6NgSgiAW0MrSyAsUCkNwXtXTVtJWZUSuzkNwHVBfAEAIkUfQyOgDJKQwodrA2MDYEBDCguEWkgRHoGFQ3A10RyogiNlDhR0WIxX0AS5DLHXcHMIMAAAAASUVORK5CYII=',
			'7E72' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA6Y6IIu2igDJgIAADLFABxFksSlAXqNDgwiy+6Kmhq1aCqSQ3McI0jUFpBKhlxWkK4ChFdktIkDI6ABUiSQWABRjBalEEQO6uYExNGQQhB8VIRb3AQDp6MvFINBNfAAAAABJRU5ErkJggg==',
			'8104' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMDQEIImJTGEMYAhlaEQWC2hlDWB0dGhFVccQwNoQMCUAyX1Lo1ZFLV0VFRWF5D6IukAHVPPAYqEhaGJAOxrQ7QC6BUWMNYA1FN3NAxV+VIRY3AcAfYHLrsSMyWoAAAAASUVORK5CYII=',
			'CB2D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQxhCGUMdkMREWkVaGR0dHQKQxAIaRRpdGwIdRJDFgCoZEGJgJ0Wtmhq2amVm1jQk94HVtTKi6210mIImBrTDIQBVDOwWB0YUt4DczBoaiOLmgQo/KkIs7gMARLLLaNwT4J4AAAAASUVORK5CYII=',
			'177D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0MdkMRYHRgaHRoCHQKQxEShYiIoehlaGRodYWJgJ63MWjVt1dKVWdOQ3AdUF8AwhRFNL0gUXYwVLI4qJtIAEkVxSwhYDMXNAxV+VIRY3AcArcPIP57mYIUAAAAASUVORK5CYII=',
			'30D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGRoCkMQCpjCGsDY6NCKLMbSytrI2BLSiiE0RaXQFqg5Act/KqGkrU1dFRUUhuw+sLtAB1TywWGgIph3Y3IIihs3NAxV+VIRY3AcAtJ/OPgVHBwkAAAAASUVORK5CYII=',
			'1361' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgdRFoZHR2mIouJOjA0ujY4hKLqZWhlbYDrBTtpZdaqsKVTVy1Fdh9YnaNDK5peoHkBRIiB3YIiJhoCdnNowCAIPypCLO4DACr8ySClXfuQAAAAAElFTkSuQmCC',
			'C2A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QPQ6AIAxGy9Ab4H1gYK8JOHCaMngDPQILp7QxMSnqqNF+20t/XgrtUgx/yit+GE2EBVanmJ1xhgREilGxxXvvrGYMJfB4sF0pt1Zry3lSftK3INN6miVMxB0rxklfd0NcWFjngnFIQfZp56/+92Bu/DYJ780Z5f049gAAAABJRU5ErkJggg==',
			'3F07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQx2mMIaGIIkFTBFpYAhlaBBBVtkq0sDo6IAqBlTH2hAAhAj3rYyaGrZ0VdTKLGT3QdS1MqCZBxSbgi4GtCOAAcMtjA6obgaKTUEVG6jwoyLE4j4Ai9/LaiD2yeoAAAAASUVORK5CYII=',
			'D7E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHVqRxQKmMDS6NjBMRRFrBYuFoom1sjYwwPSCnRS1dNW0paGrliK7D6guAEkdVIzRAVOMtQFDbIoIhlhoAFAs1CE0YBCEHxUhFvcBAIrnzSvWhqKOAAAAAElFTkSuQmCC',
			'CFFB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WENEQ11DA0MdkMREWkUaWBsYHQKQxAIaIWIiyGINKOrATopaNTVsaejK0Cwk96GpQxETIWAHNrewhoDFUNw8UOFHRYjFfQDvF8sdPdCWiwAAAABJRU5ErkJggg==',
			'65B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQrAIAxF45AbpPdx6Z6CGeppIrQ3sEdw8ZTVLdKOLZgPGR6BPD7UxyjMlF/8kBdBgcsbRpkUk2c2jI/GdPNkmVJod0rGb49XKVJrNH4hQ1qTT/YHn431PTDqLMPggmd3GZ1dQHESJujvw7z43Ruuzb1LxkgXAAAAAElFTkSuQmCC',
			'60A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQwtrK6OjoIIIs1iDS6NoQAFMHdlJk1LSVqauipmYhuS9kCoo6iN5WoFhoIKp5raytrA2oYiC3sKLpBbkZKIbi5oEKPypCLO4DAANozRniGIl2AAAAAElFTkSuQmCC',
			'8C1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIaGIImJTGFtdAhhdEBWF9Aq0uCIJiYyRaQBqBcmBnbS0qhpq1ZNWxmaheQ+NHVw87CJOUxBtwPoFjQxkJsZQx1RxAYq/KgIsbgPAH+YygCS/NfpAAAAAElFTkSuQmCC',
			'779E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUMDkEVbGRodHR0dGNDEXBsCUcWmMLSyIsQgbopaNW1lZmRoFpL7GB0YAhhCUPWygkTRzBMBiaKJBQBFGdHcAhJjQHfzAIUfFSEW9wEAAMnJmoQiEFsAAAAASUVORK5CYII=',
			'67A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmcLQ6BDKEBCAJBbQwtDo6OjoIIAs1sDQytoQ6IDsvsioVdOWropMzUJyX8gUhgCgOlTzWhkdWEMDHURQxFgbQOaJoLhFBCgWgKKXNQAshuLmgQo/KkIs7gMA1yHM2YwSfOQAAAAASUVORK5CYII=',
			'5AB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwETeENwj5p0j9STEHPHk6RDQI7kCkRqYygBCn+7vSWT6b6GKWe8oufRAILbd4w6BA5eeDGOLNO3hk2waWQvDrjN+/7sUqti/XLrZfsDcqjBEW2Lrh6imKZK20XlvF1VwaJHfzvw7z4nXrhziM9sNzZAAAAAElFTkSuQmCC',
			'E10C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEwNQBILaGAMYAhlCBBBEWMNYHR0dGBBEWMIYG0IdEB2X2jUqqilqyKzkN2Hpg6vGDY70N0SGsIaiu7mgQo/KkIs7gMANUnJ0Oqu2wUAAAAASUVORK5CYII=',
			'E630' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGVqRxQIaWFtZGx2mOqCIiTQCyYAAVLEGhkZHBxEk94VGTQtbNXVl1jQk9wU0iLYiqYOb59AQiEUM3Q5Mt2Bz80CFHxUhFvcBAHaCzhj8thUFAAAAAElFTkSuQmCC',
			'DFBA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGVqRxQKmiDSwNjpMdUAWawWKNQQEBKCLNTo6iCC5L2rp1LCloSuzpiG5D00dknmBoSGYYqjqpmDqDQ0AioUyoogNVPhREWJxHwCj8s314CvgwQAAAABJRU5ErkJggg==',
			'A180' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGAMYHR2mOiCJiUxhDWBtCAgIQBILaGUAqnN0EEFyX9TSVVGrQldmTUNyH5o6MAwNZQCaF4giBlKH3Q5UtwS0soaiu3mgwo+KEIv7ABhLygtSHvjvAAAAAElFTkSuQmCC',
			'24C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCHVqRxUSmMExldAiYiiwWAFTF2iAQiqK7ldGVFUiiuG/a0qVLV61aiuK+AJFWJHVgyOggGuqKJgZSA7QD1S0gWxwCUMRCQ8FuDg0YBOFHRYjFfQCbnssBVICLhAAAAABJRU5ErkJggg==',
			'F8CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMdkMQCGlhbGR0CHQJQxEQaXRsEHUTQ1LE2MMLUgZ0UGrUybOmqlaFZSO5DU4dkHiOaedjtwHQLppsHKvyoCLG4DwCpasyfs91tCgAAAABJRU5ErkJggg==',
			'8CEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHVqRxUSmsDa6NjBMdUASC2gVaQCKBQSgqBNpYG1gdBBBct/SqGmrloauzJqG5D40dXDzgGKhIRh2oKqDuAVVDOJmRxSxgQo/KkIs7gMAzk/LtrQQWa8AAAAASUVORK5CYII=',
			'2A6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaYGIImJTGEMYXR0CBBBEgtoZW1lbXB0YEHW3SrS6NrA6IDivmnTVqZOXZmF4r4AoDpHRwdkexkdRENdGwJRxFgbQOYFotghAhRzRHNLaKhIowOamwcq/KgIsbgPAJKQyzXX0A9/AAAAAElFTkSuQmCC',
			'6980' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gRaXRtCAgIQBZrEGl0dHR0EEFyX2TU0qVZoSuzpiG5L2QKYyCSOojeVgageYFoYiwYdmBzCzY3D1T4URFicR8AjHTMhHHcRPgAAAAASUVORK5CYII=',
			'AD39' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhDGaY6IImxBoi0sjY6BAQgiYlMEWl0aAh0EEESC2gFijU6wsTATopaOm1l1tRVUWFI7oOoc5iKrDc0FGReQAOGeQ0B6HZguCWgFdPNAxV+VIRY3AcAjvjOb+GSAlMAAAAASUVORK5CYII=',
			'13D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGVqRxVgdRFpZGx2mIouJOjA0ujYEhKLqZWhlbQiA6QU7aWXWqrClq6KWIrsPTR1MDGQeEWJgt6CIiYaA3RwaMAjCj4oQi/sAeJPKJg89QfUAAAAASUVORK5CYII=',
			'4F96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37poiGOoQyTHVAFgsRaWB0dAgIQBJjBIqxNgQ6CCCJsU6BiCG7b9q0qWErMyNTs5DcFwBUxxASiGJeaChQDKhXBMUtQHuxiaG5BSTGgO7mgQo/6kEs7gMAEUTLal/eNssAAAAASUVORK5CYII=',
			'16BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaYGIImxOrC2sjY6BIggiYk6iDSyNgQ6sKDoFWlgbXR0QHbfyqxpYUtDV2Yhu4/RQbQVSR1Mb6Mr0DxsYqh2YHFLCKabByr8qAixuA8A4x3I34MgsYEAAAAASUVORK5CYII=',
			'3DA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANEQximMIaGIIkFTBFpZQhlaBBBVtkq0ujo6IAqNkWk0bUhAAgR7lsZNW1l6qqolVnI7oOoa2VAM881NGAKhlhDQAADmltYGwId0N2MLjZQ4UdFiMV9AJQnzUpxnKOlAAAAAElFTkSuQmCC',
			'EA05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIYGIIkFNDCGMIQyOjCgiLG2Mjo6oomJNLo2BLo6ILkvNGraytRVkVFRSO6DqAOSKHpFQzHFRBodgXagizmEMgQguy80BCg2hWGqwyAIPypCLO4DAP5MzU7odhToAAAAAElFTkSuQmCC',
			'B21E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMIYGIIkFTGFtZQhhdEBWF9Aq0uiILjaFodFhClwM7KTQqFVLV01bGZqF5D6gOiBEN48hAFMMyEcXm8LagC4WGiAa6giEyG4eqPCjIsTiPgCBq8rdYmVVgQAAAABJRU5ErkJggg==',
			'203B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGUMdkMREpjCGsDY6OgQgiQW0srYyNAQ6iCDrbhVpdECog7hp2rSVWVNXhmYhuy8ARR0YMjoAxdDMY23AtEOkAdMtoaGYbh6o8KMixOI+ABffy1IT5L6EAAAAAElFTkSuQmCC',
			'FE51' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHVqRxQIaRBpYGximYhELxRCbygDTC3ZSaNTUsKWZWUuR3QdSByQx7MAmxopFjNER3X2ioUCXhAYMgvCjIsTiPgD8F8ztP4u2/QAAAABJRU5ErkJggg==',
			'2A4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHUNDkMREpjCGMLQ6OiCrC2hlbWWYiirG0CrS6BAIF4O4adq0lZmZmaFZyO4LEGl0bUTVy+ggGuoaGogixtoANA9NnQgWsdBQTLGBCj8qQizuAwAwJMrCi5ubvwAAAABJRU5ErkJggg==',
			'5C01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMLQiiwU0sDY6hDJMRRUTaXB0BIoiiQUGiDSwNgTA9IKdFDZt2qqlq6KWorivFUUdTrGAVrAdKGIiU8BuQRFjDQC7OTRgEIQfFSEW9wEAFj/M00aWNB0AAAAASUVORK5CYII=',
			'450A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpI37poiGMkxhaEURCxFpYAhlmOqAJMYIFGN0dAgIQBJjnSISwtoQ6CCC5L5p06YuXboqMmsakvsCpjA0uiLUgWFoKFgsNATFLSKNjo6OKOoYprC2MoQyookxhgAxqthAhR/1IBb3AQAX7MtLuFBwFgAAAABJRU5ErkJggg==',
			'4F8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpI37poiGOoQyhgYgi4WINDA6Ojogq2MEirE2BKKIsU5BUQd20rRpU8NWha4MzUJyX8AUTPNCQzHNY5iCXQxdL0iMAd3NAxV+1INY3AcAppPJd9UjWVsAAAAASUVORK5CYII=',
			'1F3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1DGUMdkMRYHUQaWBsdHQKQxESBYgwNgWASoRfIQ6gDO2ll1tSwVVNXhmYhuQ9NHUIMm3lYxDDcEiLSwIjm5oEKPypCLO4DAJ26yVxRrvgoAAAAAElFTkSuQmCC',
			'200A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGWY6oAkFtDK2sro6BAQgKy7VaTRtSHQQQTZfdOmrUxdFZk1Ddl9ASjqwJDRASwWGoLslgaQHY4o6kQaQG5hRBELDQW5GVVsoMKPihCL+wAbQcpcw4h5xAAAAABJRU5ErkJggg==',
			'4474' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM3QrRGAMAyG4S+iG8A+NfiIxnSaVnSD0A1qOiXgwo9AwEHi3rvcPRf00yT8ad/xKYoTTmxbwIzE2TYKkLUV25zShOyVja/W1nrrMRof61Cg5O2tyCieScLBQh57y+ZLN9pX/3tuL3wLsy/NVBsuF8wAAAAASUVORK5CYII=',
			'C8E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHVqRxURaWVtZGximOiCJBTSKNLo2MAQEIIs1gNQxOogguS9q1cqwpaErs6YhuQ9NHVQMZB6aGBY7sLkFm5sHKvyoCLG4DwDsGswCQQLyywAAAABJRU5ErkJggg==',
			'73CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNZQxhCHUMDkEVbRVoZHQIdUFS2MjS6Ngiiik1haGVtYISJQdwUtSps6aqVoVlI7gOqQFYHhqwNIPNQxUQaMO0IaMB0S0ADFjcPUPhREWJxHwDIBsmRzk7CgQAAAABJRU5ErkJggg==',
			'B7A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMDQEIIkFTGFodAhlaEQRa2VodHR0aEVT18oKJAOQ3BcatWra0lVRUVFI7gOqC2BtCHRANY/RgTU0MDQERYy1AWgemltEMMRCAzDFBir8qAixuA8A/OTQIvaxRZMAAAAASUVORK5CYII=',
			'53D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGaY6IIkFNIi0sjY6BASgiDE0ujYEOoggiQUGMLSyglQjuS9s2qqwpauigBDJfa1gdY3IdgDFgOYFtCK7JQAiNgVZTGQKxC3IYqwBIDczhoYMgvCjIsTiPgD69M12fOVnjAAAAABJRU5ErkJggg==',
			'07A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ3DMAwEyYIb0Pu8ivR0oSKahiq8geIN3GhKC6koJGUCiN8dnsSB1D/GaaX8xY+xZTTOFpgYVWRG7GmjmlKamB10iO8PBL9y9fPqz1KC3+iZuLlOuwzJM9MmPu5BJxcdzCz6Md7shQX+98N88bsBNh7LrjQ8rqIAAAAASUVORK5CYII=',
			'C476' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYWllDA6Y6IImJtDJMZWgICAhAEgtoZAhlaAh0EEAWa2B0ZWh0dEB2X9SqpUtXLV2ZmoXkvgCQiVMYUc1rEA11CGB0EEG1o5XRAVUMqLOVtYEBRS/YzQ0MKG4eqPCjIsTiPgD+PMvexamYpAAAAABJRU5ErkJggg==',
			'2984' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGRoCkMREprC2Mjo6NCKLBbSKNLoCSWQxBqCYo6PDlABk901bujQrdFVUFLL7AhgDHYEKkfUyOjAAzQsMDUF2SwMLyA5UtzSA3YIiFhqK6eaBCj8qQizuAwCTW81TT7AsNwAAAABJRU5ErkJggg==',
			'CF8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQx1CGUMdkMREWkUaGB0dHQKQxAIaRRpYGwIdRJDFGlDUgZ0UtWpq2KrQlaFZSO5DUwcXwzAPix3Y3MIaAlSB5uaBCj8qQizuAwBTocuBl2LqdQAAAABJRU5ErkJggg==',
			'F9C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHVqRxQIaWFsZHQKmooqJNLo2CIRiijHA9IKdFBq1dGnqqlVLkd0X0MAYiKQOKsbQiCnGArIDm1vQxMBuDg0YBOFHRYjFfQDwx82o9yVqygAAAABJRU5ErkJggg==',
			'503F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGUNDkMQCGhhDWBsdHRhQxFhbGRoCUcQCA0QaHRDqwE4KmzZtZdbUlaFZyO5rRVGHEEMzL6AV0w6RKZhuYQ0AuxnVvAEKPypCLO4DADVHylzxEXybAAAAAElFTkSuQmCC',
			'E147' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYAhgaHUNDkMQCGhgDGFodGkRQxFgDGKaiiwH1BjqAaLj7QqNWRa3MzFqZheQ+kDrWRodWBjS9rKEBU9DFGBodAjDFHB1Q3cwaii42UOFHRYjFfQC9esubYy1GMAAAAABJRU5ErkJggg==',
			'CAD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYAlhDGUIdkMREWhlDWBsdHQKQxAIaWVtZGwIaRJDFGkQaXYFkAJL7olZNW5m6KmppFpL70NRBxURDXdHNa4SoE0FxC1AMzS2sIUAxNDcPVPhREWJxHwBhsc8PdMUsNgAAAABJRU5ErkJggg==',
			'CE00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WENEQxmmMLQii4m0ijQwhDJMdUASC2gUaWB0dAgIQBZrEGlgbQh0EEFyX9SqqWFLV0VmTUNyH5o63GJY7MDmFmxuHqjwoyLE4j4A7ZbMAACTTD8AAAAASUVORK5CYII=',
			'8EFB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MdkMREpog0sDYwOgQgiQW0QsREcKsDO2lp1NSwpaErQ7OQ3EeseUTYgXBzAyOKmwcq/KgIsbgPAB+HyoGz/5nwAAAAAElFTkSuQmCC',
			'3A9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGUNDkMQCpjCGMDo6OqCobGVtZW0IRBWbItLoihADO2ll1LSVmZmRoVnI7gOqcwhB09sqGuqAbl6rSKMjmlgAUK8jmltEA4DmhTKi6h2g8KMixOI+AFLYyhGSSBSMAAAAAElFTkSuQmCC',
			'4358' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37prCGsIY6THVAFgsRaWVtYAgIQBJjDGFodG1gdBBBEmOdwtDKOhWuDuykadNWhS3NzJqaheS+AKA6IIliXmgoQ6NDQyCKeQxTQHagi4m0Mjo6oOgFuZkhlAHVzQMVftSDWNwHALnoy/JLyOnoAAAAAElFTkSuQmCC',
			'B430' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGVqRxQKmMExlbXSY6oAs1soQCiQDAlDUMboyNDo6iCC5LzRq6dJVU1dmTUNyX8AUkVYkdVDzREMdGgLRxEDuQLeDoRXdLdjcPFDhR0WIxX0AlJ/OKduszU4AAAAASUVORK5CYII=',
			'3347' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANYQxgaHUNDkMQCpoi0MrQ6NIggq2xlaHSYiiY2BSga6NAQgOS+lVGrwlZmZq3MQnYfUB1ro0MrA5p5rqEBU9DFHBodAhjQ3dLo6IDFzShiAxV+VIRY3AcA27bMg9LVS8gAAAAASUVORK5CYII=',
			'85E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoBYkxOgigqgsBiSG7b2nU1KVLQ1emZiG5T2QKQ6NrAyOaeWAxBxFUOzDERKawtqK7hTWAMQTdzQMVflSEWNwHAEo1y42dwZH3AAAAAElFTkSuQmCC',
			'75F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA6Y6IIu2ijSwNjAEBGCIMTqIIItNEQkBqmsQQXZf1NSlS0OBFJL7GB0YGl0bGBqR7QDqA4m1IrtFpEEEJDYFWSyggbUV5BZUMUagvYyhIYMg/KgIsbgPAHmZy6cLccHZAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>