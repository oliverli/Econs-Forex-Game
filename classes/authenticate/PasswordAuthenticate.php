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

    /**
     * Description of PasswordAuthenticate
     *
     * @author Li Yicheng <liyicheng340 [at] gmail [dot com]>
     */
    require_once("mysql/UniversalConnect.php");
    require_once("IAuthenticator.php");

    class PasswordAuthenticate implements IAuthenticator
    {

        public function authenticate($username = null, $password = null)
        {
            if(is_null($username) || is_null($password))
                return false;
            $db = UniversalConnect::doConnect();
            $usernameToCheck = $db->real_escape_string(trim($username));
            $passwordToCheck = $db->real_escape_string(trim($password));
            $query = "SELECT userkey, password, usertype FROM users WHERE userid=\"$usernameToCheck\" LIMIT 1";
            $result = $db->query($query) or die($db->error.$query);
            if($result->num_rows < 1)
                return false;
            while($row = $result->fetch_assoc())
            {
                if(password_verify($passwordToCheck, $row["password"]))
                {
                    if(password_needs_rehash($row["password"], PASSWORD_DEFAULT))
                    {
                        $newHash = password_hash($passwordToCheck, PASSWORD_DEFAULT);
                        $query = "UPDATE users SET password=\"$newHash\" WHERE userkey=".$row["userkey"];
                        $db->query($query);
                    }
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

    }
    