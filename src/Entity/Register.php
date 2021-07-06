<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Register 
{
    private $firstName;

    private $lastName;

    private $mail;

    private $password;

    private $passwordVerif;

    private $adress;

    private $city;

    private $codePostal;

    private $country;

    private $phoneNumber;

    public function getFirstName(){
        return $this->firstName;
    }
    public function setFirstName($firstName){
        $this->firstName = $firstName;
        return $this;
    }
    public function getLastName(){
        return $this->lastName;
    }
    public function setLastName($lastName){
        $this->lastName = $lastName;
        return $this;
    }
    public function getMail(){
        return $this->mail;
    }
    public function setMail($mail){
        $this->mail = $mail;
        return $this;
    }
    public function getPassword(){
        return $this->password;
    }
    public function setPassword($password){
        $this->password = $password;
        return $this;
    }
    public function getPasswordVerif(){
        return $this->passwordVerif;
    }
    public function setPasswordVerif($passwordVerif){
        $this->passwordVerif = $passwordVerif;
        return $this;
    }
    public function getAdress(){
        return $this->adress;
    }
    public function setAdress($adress){
        $this->adress = $adress;
        return $this;
    }
    public function getCity(){
        return $this->city;
    }
    public function setCity($city){
        $this->city = $city;
        return $this;
    }
    public function getCodePostal(){
        return $this->codePostal;
    }
    public function setCodePostal($codePostal){
        $this->codePostal = $codePostal;
        return $this;
    }
    public function getPhoneNumber(){
        return $this->phoneNumber;
    }
    public function setPhoneNumber($phoneNumber){
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
    public function getCountry(){
        return $this->country;
    }
    public function setCountry($country){
        $this->country = $country;
        return $this;
    }
}