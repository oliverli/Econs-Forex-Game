<?php

    /*
     * The MIT License
     *
     * Copyright 2015 Li Yicheng, Sun Yudong, and Walter Kong.
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

    /**
     * Description of PrivilegeAuthenticate
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("SessionAuthenticate.php");
    require_once("IAuthenticator.php");

    class PrivilegeAuthenticate implements IAuthenticator
    {

        public function authenticate($usertype = -1)
        {
            if($usertype === -1)
            {
                $SessAuthWorker = new SessionAuthenticate();
                if(!$SessAuthWorker->authenticate())
                    return false;
                if(session_status() === PHP_SESSION_NONE)
                {
                    session_start();
                }
                return $this->authenticate($_SESSION["usertype"]);
            }
            else
            {
                if($usertype == 2)
                    return true;
                else
                    return false;
            }
        }

    }
    