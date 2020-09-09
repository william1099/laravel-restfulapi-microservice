<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use App\Exceptions\NotAdminException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

trait ApiResponser {

    protected function successResponse($data, $code) {
        if($data["data"] instanceof Model) {
            return response()->json($data, $code);
        }
        
        $data = $this->sortModel($data["data"]);
        $data = $data->values();
        $data = $this->paginate($data);
        $data = $this->cacheResponse($data);
        // //dd($paginated->getCollection());
        // $paginated = $paginated->getCollection()->push($paginated->currentPage());
        // //dd($datas->first()->name);
        
        return response()->json($data, $code);
    }

    protected function errorResponse($msg, $code) {
        return response()->json($msg, $code);
    }

    protected function transformData($data, $transformer) {
        
        $transformedData = fractal($data, new $transformer);
        //dd($transformedData->getData());
        //return collect($transformedData["data"]);
        return $transformedData->getData();
        
    }

    protected function sortModel($collection) {

        $collection = $this->filterData($collection);

        if(request()->has("sort_by")) {
            $attribute = request()->get("sort_by");

            if($collection->isEmpty()) {
                throw new NotAdminException();
            }
            
            if(!$collection->first()->hasAttribute($attribute)) {
                throw new NotAdminException();
            }
            
            return $collection->sortBy($attribute);
        }

        return $collection;
    }

    protected function filterData($collection) {
        $exception = ["sort_by", "page", "per_page"];
        foreach(request()->query() as $key => $value) {
            //$attr = $transformer::originalAttribute($key);

            if(isset($key, $value) && !in_array($key, $exception)) {
                $collection = $collection->where($key, $value);
            }
        }

        return $collection;
    }

    public function paginate($collection) {

        $rules = [
            "per_page" => "integer|min:2|max:50"
        ];

        Validator::validate(request()->all(), $rules);
        
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;

        if(request()->has("per_page")) {
            $perPage = request()->get("per_page");
        }
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        $paginated->appends(request()->all());

        return $paginated;

    }

    public function cacheResponse($data) {
        $url = request()->url();
        $queryParams = request()->query();
        
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";

        if(Cache::store("file")->has($fullUrl)) {
            return Cache::store("file")->get($fullUrl);
        }   

        $data->isCached = "true";
        Cache::store("file")->put($fullUrl, $data, 20);
        
        return $data;
    }
}
