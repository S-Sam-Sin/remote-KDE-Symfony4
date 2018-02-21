<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashBoardController extends Controller
{
    private $_mainUser;
    private $_users;
    private $_groups;
    private $_services;

     /**
      * @Route("/")
     */
    public function index()
    {
        exec("awk -F':' '{ print $1}' /etc/passwd",$this->_users);
        exec("cut -d: -f1 /etc/group",$this->_groups);
        exec("service --status-all", $this->_services);
        exec("cat /etc/passwd|grep 1000", $this->_mainUser);
        $this->_mainUser = explode( ":", $this->_mainUser[0]);
        dump($this->_mainUser);

        return $this->render('dashboard.html.twig', array(
            "mainUser"      => $this->_mainUser,
            "services"      => $this->_services,
            "TotalServices" => count($this->_services),
            "users"         => $this->_users,
            "TotalUsers"    => count($this->_users),
            "groups"        => count($this->_groups),
            "TotalGroups"   => count($this->_groups),
        ));
    }
}