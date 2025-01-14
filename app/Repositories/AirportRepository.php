<?php

namespace App\Repositories;

use App\Interfaces\AirportRepositoryInterface;
use App\Models\Airport;

class AirportRepository implements AirportRepositoryInterface
{
    public function getAllAirports()
    {
        return Airport::all();
    }

    public function getAllAirportBySlug($slug)
    {
        return Airport::where('slug', $slug)->first();
    }

    public function getAllAirportByIataCode($iataCode)
    {
        return Airport::where('iata_code', $iatacode)->first();
    }

}