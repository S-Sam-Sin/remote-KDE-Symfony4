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
    private $_servicesActive;
    private $_servicesDisabled;
    private $_logs;
    private $_ufw;
    private $_dpkg;

     /**
      * @Route("/")
     */
    public function index()
    {
        exec("awk -F':' '{ print $1}' /etc/passwd",$this->_users);
        exec("cut -d: -f1 /etc/group",$this->_groups);
        exec("cat /etc/passwd|grep 1000", $this->_mainUser);
        $this->_mainUser = explode( ":", $this->_mainUser[0]);

        exec("service --status-all", $this->_services);

        $count = 0;
        foreach ($this->_services as $service)
        {
//            dump($service);
            if (substr($service, 1,5) == "[ + ]")
            {
                $this->_servicesActive[$count] = $service;
            }
            else{
                $this->_servicesDisabled[$count] = $service;
            }
            $count++;
        }

        exec("ufw version", $this->_ufw);
        exec("dpkg --version", $this->_dpkg);

        exec("tail -n 5 /var/log/ufw.log", $this->_logs[0]);
        exec("tail -n 5 /var/log/gufw.log", $this->_logs[1]);
        exec("tail -n 5 /var/log/dpkg.log", $this->_logs[2]);
//        dump($this->_services);

        return $this->render('dashboard.html.twig', array(
            "os"            => php_uname("n"),
            "php"           => phpversion(),
            "zend"          => zend_version(),
            "mainUser"      => $this->_mainUser,
            "services"      => $this->_services,
            "servicesActive"      => count($this->_servicesActive),
            "servicesDisabled"      => count($this->_servicesDisabled),
            "TotalServices" => count($this->_services),
            "users"         => $this->_users,
            "TotalUsers"    => count($this->_users),
            "groups"        => count($this->_groups),
            "TotalGroups"   => count($this->_groups),
            "dpkgVersion"   => $this->_dpkg,
            "ufwVersion"   => $this->_ufw,
            "logs"          => $this->_logs,
        ));
    }
}