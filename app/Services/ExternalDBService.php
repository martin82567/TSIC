<?php


namespace App\Services;


interface ExternalDBService
{
    // create the instance to the external DB, by loggin in
    public function authenticate();

    //
    public function getMentorsByOffice();
}
