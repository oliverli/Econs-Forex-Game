<?php

    /*
     * The MIT License
     *
     * Copyright 2016 Li Yicheng, Sun Yudong, and Walter Kong.
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */

    //adds ./classes/ to include directory so that all the require_once statements will work
    set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__)."/classes/");
    require_once("pages/LoginHome.php");

    //all HTML is generated in the LoginHome constructor - edit ./classes/pages/LoginHome.php to update the page
    $worker = new LoginHome();
<<<HTML
    <script>
        function failed(){
            $("#login-card").removeClass("failed")
                            .addClass("failed")
                            .delay(1000)
                            .queue(function() {
                                $(this).removeClass("failed");
                                $(this).dequeue();
                            });
        }

        $(window).load(function() {
            $('#username').focus();
        });
    </script>
HTML;
?>
