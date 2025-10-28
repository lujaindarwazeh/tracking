<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\storeway;
use App\Models\way;
use App\Http\Resources\storewayResource;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\addpoints;

 

class WayController extends Controller
{

  

    

    public function store(storeway $request)
    {
        $way = new way();
        $way->fill($request->all());
        $way->save();
        return response()->json(new storewayResource($way), 201);
    
        
    }


   public function addPoints(addpoints $request, $wayId){
    $way = Way::findOrFail($wayId);

    // Check if points already exist for this way
    $existing = DB::table('points')->where('way_id', $wayId)->exists();

    if ($existing) {
        // Delete the existing entry
        DB::table('points')->where('way_id', $wayId)->delete();
    }

    // Store all points as a single JSON object
    DB::table('points')->insert([
        'way_id' => $wayId,
        'locations' => json_encode($request->points),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Points stored as JSON successfully']);
}


    
    private function convertToWKT(array $coordinates): ?string
    {
        if (count($coordinates) < 2) {
            return null;
        }

        $lineString = implode(', ', array_map(function ($coord) {
            return "{$coord['x']} {$coord['y']}";
        }, $coordinates));

        return "LINESTRING($lineString)";
    }



  public function generatePolygon($wayId)
  {


    $way = Way::findOrFail($wayId);

    

    $raw = DB::table('points')
        ->where('way_id', $way->id)
        ->value('locations');

    if (!$raw) {
        return response()->json(['error' => 'No coordinates found'], 404);
    }

    $decoded = json_decode($raw, true);

    if (!is_array($decoded) || count($decoded) < 2) {
        return response()->json(['error' => 'At least 2 points are required'], 400);
    }

    $coordinates = $decoded;

    $geometry = $this->convertToWKT($coordinates);

    if (!$geometry) {
        return response()->json(['error' => 'Invalid coordinates provided'], 422);
    }

    $polygon = DB::selectOne("
        SELECT ST_AsText(ST_Buffer(ST_GeomFromText('$geometry'), 0.0002)) AS poly
    ");

    // 🧹 Parse WKT polygon into array of coordinates
    $clean = str_replace(['POLYGON((', '))'], '', $polygon->poly);
    $pairs = explode(',', $clean);
    $polygonCoordinates = array_map(function ($pair) {
        [$x, $y] = explode(' ', trim($pair));
        return ['x' => (float) $x, 'y' => (float) $y];
    }, $pairs);

    DB::table('polygons')->where('way_id', $way->id)->delete();

    DB::table('polygons')->insert([
        'way_id' => $way->id,
        'coordinate' => json_encode($polygonCoordinates), // ✅ now storing polygon coordinates
        'geometry' => DB::raw("ST_GeomFromText('{$polygon->poly}', 4326)"),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Polygon generated and stored successfully']);
}

 public function getgeneratePolygon($wayId)
 {
    $way = Way::findOrFail($wayId);

    $polygon = DB::table('polygons')
        ->where('way_id', $way->id)
        ->select('coordinate')
        ->first();

    if (!$polygon) {
        return response()->json(['error' => 'Polygon not found for this way'], 404);
    }

    return response()->json([
        'polygon' => json_decode($polygon->coordinate, true)
    ]);
}


public function getgeneratePolygongeometry($wayId)
 {
    $way = Way::findOrFail($wayId);

    $polygon = DB::table('polygons')
        ->where('way_id', $way->id)
        ->select(DB::raw('ST_AsText(geometry) as geometry_wkt'))
        ->first();

    if (!$polygon) {
        return response()->json(['error' => 'Polygon not found for this way'], 404);
    }

    return response()->json([
        'geometry_wkt' => $polygon->geometry_wkt
    ]);






}
}
