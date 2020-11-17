<?PHP

class parser {

    // Array for error messages
    private $error = array();

    // Array for redefined messages
    private $vars = array();

    // Pointer to parsing file
    private $tpl_file;

    // Pointer to file output
    private $template;

    // Error message inline style
    private $line_1 = null;
    private $line_2 = null;

    function __construct($tpl)
    {
        $this->tpl_file = "./tpl/".$tpl;

        // Define error messages
        $this->error[0] = "Server needs to update!";
        $this->error[1] = "HTML template not found!";

        // Check for existen function
        if (!@function_exists("file_get_contents")) {
                $this->halt( $this->error[0] );
                return false;
        }
        return $this->error;
    }

    public function set_tpl($key, $var)
    {
            $this->vars[$key] = $var;
            return $this->vars;
    }

    public function get_tpl()
    {
            if(empty($this->tpl_file) || !file_exists($this->tpl_file)) {
                    $this->halt( $this->error[1] );
            } else {
                    $this->template = file_get_contents($this->tpl_file);
            }
            return $this->template;
    }

    function tpl_parse()
    {
        foreach($this->vars as $find => $replace) {
            $this->template = str_replace($find, $replace, $this->template);
        }
        return $this->template;
    }

    function halt($msg)
    {
        $init = "<strong>ERROR: </strong>";
        $this->line_1 = "<div align='center' style='font-family:Courier;font-size:12px;'>";
        $this->line_2 = "</div>";
        die($this->line_1.$init.$msg.$this->line_2);
    }
};
?>
