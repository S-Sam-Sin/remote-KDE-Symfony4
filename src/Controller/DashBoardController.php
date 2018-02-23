<?php

namespace App\Controller;

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashBoardController extends Controller
{
    private $_os;
    private $_mainUser;
    private $_users;
    private $_groups;
    private $_services;
    private $_logs;
    private $_version;
    private $_bin;
    private $_kernel;
    private $_output;


    public function __construct()
    {
        $this->_os = [  "name" => php_uname("n"),
                        "phpversion" => phpversion(),
                        "zendversion" => zend_version(),
        ];
    }

    /**
      * @Route("/")
     */
    public function index()
    {
        # Get kernels
        $this->_kernel = $this->SoftwareInstalled("linux-image");

        # Users anf groups
        exec("awk -F':' '{ print $1}' /etc/passwd",$this->_users);
        exec("cut -d: -f1 /etc/group",$this->_groups);
        exec("cat /etc/passwd|grep 1000", $this->_mainUser);
        $this->_mainUser = explode( ":", $this->_mainUser[0]);

        # Services
        $this->_services = $this->Services("total");

        # Binary Folders.
        $this->_bin = $this->CountFiles(["/usr/local/sbin/",
                                        "/usr/local/bin/",
                                        "/usr/bin/",
                                        "/usr/sbin/",
                                        "/sbin/",
                                        "/bin/"]);

        # Software versions.
        $this->_version = [ "ufw"   => $this->SoftwareVersion(FALSE,"ufw"),
                            "dpkg"  => $this->SoftwareVersion(TRUE,"dpkg"),
        ];

        # Log Entries
        $this->_logs = $this->LogEntries([5,10,9], ["ufw","gufw", "dpkg"]);

//        dump($this->_os);

        return $this->render('dashboard.html.twig', array(
            "os"                => $this->_os,
            "kernels"           => $this->_kernel,
            "mainUser"          => $this->_mainUser,
            "services"          => $this->_services,
            "servicesActive"    => count($this->_services["enabled"]),
            "servicesDisabled"  => count($this->_services["disabled"]),
            "users"             => $this->_users,
            "TotalUsers"        => count($this->_users),
            "groups"            => count($this->_groups),
            "TotalGroups"       => count($this->_groups),
            "binary"            => $this->_bin,
            "version"           => $this->_version,
            "logs"              => $this->_logs,
        ));
    }

    public function Services(string $procedure) :array
    {
        unset($this->_output);
        switch ($procedure)
        {
            case "total":
                exec("service --status-all", $services);

                $i = 0;
                foreach ($services as $service)
                {
                    if (substr($service, 1,5) == "[ + ]")
                    {
                        $this->_output["enabled"][$i] = $service;
                    }
                    else{
                        $this->_output["disabled"][$i] = $service;
                    }
                    $i++;
                }
                return $this->_output;
                break;

            default:
                return $this->_output = [];
        }
    }

    public function SoftwareInstalled(string $grep) : array
    {
        unset($this->_output);
        exec("dpkg --list | grep ".$grep, $softwares);
        $count = 0;
        foreach ($softwares as $software)
        {
            $this->_output[$count] = substr($software,4,40);
            $count++;
        }
        return $this->_output;
    }

    public function SoftwareVersion(bool $binding, string $software) : array
    {
        unset($this->_output);
        if($binding)
        {
            $binding = "--";
        }
        elseif($binding == FALSE)
        {
            $binding = "";
        }
        exec($software." ".$binding."version",$this->_output);
        return $this->_output;
    }

    public function LogEntries(array $lines, array $logs) : array
    {
        unset($this->_output);
        if(count($lines) == count($logs))
        {
            for($i = 0;$i < count($lines);$i++)
            {
                 exec("tail -n ".$lines[$i]." /var/log/".$logs[$i].".log", $this->_output[$i]);
            }
            return $this->_output;
        }
        else {
            return [];
        }
    }

    public function CountFiles(array $folders) : array
    {
        unset($this->_output);
        $i = 0;
        foreach ($folders as $folder)
        {
            $this->_output[$i] = exec("ls -1 ".$folder." | wc -l");
            $i++;
        }
        return $this->_output;
    }
}