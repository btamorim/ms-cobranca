<?php

namespace App\Services;

abstract class BaseService
{
    abstract function repository();
    
    /**
     * Show all resgisters
     *
     * @param Int $paginate
     * @param String $currentPage
     * @param Array $dataForm
     * @return Collection
     */
    public function getAll($paginate = null, $currentPage = null, $dataForm = null)
    {
        return $this->repository()->getAll($paginate, $currentPage, $dataForm);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\  $request
     * @return mix
     */
    public function store($request)
    {
        return $this->repository()->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findOrFail($id)
    {
        return $this->repository()->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($request, $id)
    {
        return $this->repository()->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->repository()->destroy($id);
    }
}
