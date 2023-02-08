<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SubmissionFiles;
use App\Models\Transaction;

class SubmissionFilesController extends Controller
{
    public function upload(Request $request, $id){
        try{
            if(isset($request->file)){
                foreach($request->all()['file'] as $file){
                    $data = (object) [
                        'original' => $file,
                        'transaction_id' => $id,
                        'name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'is_image' => substr($file->getMimeType(), 0, 5) == 'image' ? true : false,
                        'size' => $file->getSize(),
                    ];

                    $data->name = date('now').rand('10000','99999').'.'.$data->extension;

                    $upload = SubmissionFiles::create((array) $data);

                    if(!$upload){
                        SubmissionFiles::firstWhere('transaction_id',$id)->forceDeleted();
                        return $this->getResponse([],'File gagal disimpan',500);
                    }

                    $data->original->storeAs('assignment', $data->name);
                }

                return $this->getResponse(Transaction::with(['submission_files'])->find($id),'File berhasil disimpan');
            }

            return true;
        }
        catch(\Exception $e){
            return $this->getResponse([],$e->getMessage(),500);
        }
    }

    public function render_file($id){
        $query = SubmissionFiles::find($id)->name;
        return response()->file(Storage::path('assignment/'.$query));
    }

    public function delete($id){
        try{
            $file = SubmissionFiles::find($id);

            if(!$file){
                return $this->getResponse([],'File tidak ditemukan',404);
            }

            $query = $file->forceDelete();

            if(!$query){
                return $this->getResponse([],'File gagal dihapus', 500);
            }

            return $this->getResponse($file, 'File pengajuan berhasil dihapus', 200);
        }
        catch(\Exception $e){
            return $this->getResponse([], $e->getMessage(),500);
        }
    }
}
