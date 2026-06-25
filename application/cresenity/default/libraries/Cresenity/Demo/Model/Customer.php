<?php

namespace Cresenity\Demo\Model;

/**
 * Demo customer model with 20 dummy records.
 *
 * @property-read int    $customer_id
 * @property-read string $name
 * @property-read string $email
 * @property-read string $phone
 * @property-read string $city
 * @property-read string $status
 */
class Customer extends \CModel {
    use \CModel_ArrayDriver_ArrayDriverTrait;

    /**
     * @var array
     */
    protected $rows = [
        ['customer_id' => 1, 'name' => 'James Smith', 'email' => 'james.smith@example.com', 'phone' => '08808637542', 'city' => 'Jakarta', 'status' => 'active'],
        ['customer_id' => 2, 'name' => 'John Johnson', 'email' => 'john.johnson@example.com', 'phone' => '08887846414', 'city' => 'Surabaya', 'status' => 'active'],
        ['customer_id' => 3, 'name' => 'Robert Williams', 'email' => 'robert.williams@example.com', 'phone' => '08748747335', 'city' => 'Bandung', 'status' => 'inactive'],
        ['customer_id' => 4, 'name' => 'Michael Brown', 'email' => 'michael.brown@example.com', 'phone' => '08863451924', 'city' => 'Medan', 'status' => 'inactive'],
        ['customer_id' => 5, 'name' => 'William Jones', 'email' => 'william.jones@example.com', 'phone' => '08214837113', 'city' => 'Semarang', 'status' => 'active'],
        ['customer_id' => 6, 'name' => 'David Garcia', 'email' => 'david.garcia@example.com', 'phone' => '08529389014', 'city' => 'Makassar', 'status' => 'inactive'],
        ['customer_id' => 7, 'name' => 'Richard Miller', 'email' => 'richard.miller@example.com', 'phone' => '08272458954', 'city' => 'Palembang', 'status' => 'active'],
        ['customer_id' => 8, 'name' => 'Joseph Davis', 'email' => 'joseph.davis@example.com', 'phone' => '08633267572', 'city' => 'Denpasar', 'status' => 'inactive'],
        ['customer_id' => 9, 'name' => 'Thomas Rodriguez', 'email' => 'thomas.rodriguez@example.com', 'phone' => '08713608295', 'city' => 'Yogyakarta', 'status' => 'active'],
        ['customer_id' => 10, 'name' => 'Charles Martinez', 'email' => 'charles.martinez@example.com', 'phone' => '08195544706', 'city' => 'Malang', 'status' => 'active'],
        ['customer_id' => 11, 'name' => 'Sarah Anderson', 'email' => 'sarah.anderson@example.com', 'phone' => '08342285876', 'city' => 'Solo', 'status' => 'active'],
        ['customer_id' => 12, 'name' => 'Jessica Taylor', 'email' => 'jessica.taylor@example.com', 'phone' => '08500961111', 'city' => 'Bogor', 'status' => 'active'],
        ['customer_id' => 13, 'name' => 'Emily Thomas', 'email' => 'emily.thomas@example.com', 'phone' => '08111989541', 'city' => 'Tangerang', 'status' => 'active'],
        ['customer_id' => 14, 'name' => 'Hannah Jackson', 'email' => 'hannah.jackson@example.com', 'phone' => '08880932287', 'city' => 'Bekasi', 'status' => 'active'],
        ['customer_id' => 15, 'name' => 'Olivia White', 'email' => 'olivia.white@example.com', 'phone' => '08887716372', 'city' => 'Depok', 'status' => 'inactive'],
        ['customer_id' => 16, 'name' => 'Emma Harris', 'email' => 'emma.harris@example.com', 'phone' => '08506710475', 'city' => 'Batam', 'status' => 'active'],
        ['customer_id' => 17, 'name' => 'Sophia Martin', 'email' => 'sophia.martin@example.com', 'phone' => '08553811733', 'city' => 'Manado', 'status' => 'active'],
        ['customer_id' => 18, 'name' => 'Isabella Thompson', 'email' => 'isabella.thompson@example.com', 'phone' => '08155189739', 'city' => 'Pontianak', 'status' => 'active'],
        ['customer_id' => 19, 'name' => 'Mia Moore', 'email' => 'mia.moore@example.com', 'phone' => '08450819632', 'city' => 'Balikpapan', 'status' => 'inactive'],
        ['customer_id' => 20, 'name' => 'Charlotte Clark', 'email' => 'charlotte.clark@example.com', 'phone' => '08927888186', 'city' => 'Banjarmasin', 'status' => 'active'],
    ];
}
