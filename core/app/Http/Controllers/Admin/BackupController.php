<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use App\Models\Backup;
use Session;

class BackupController extends Controller
{
    public function index()
    {
        $data['backups'] = Backup::orderBy('id', 'DESC')->paginate(10);
        return view('admin.backup', $data);
    }

    public function store()
    {
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        
        $ts = time();
  
        $path = 'core/storage/app/public/';
        $file = date('Y-m-d-His', $ts) . '-dump-' . $database . '.sql';
        $command = sprintf('mysqldump -h %s -u %s -p\'%s\' %s > %s', $host, $username, $password, $database, $path . $file);
  
        @mkdir($path, 0755, true);
  
        exec($command);
  
        $backup = new Backup;
        $backup->filename = $file;
        $backup->save();
  
        Session::flash('success', 'Backup saved successfully');
        return back();
    }

    public function download(Request $request)
    {
        return response()->download('core/storage/app/public/' . $request->filename, 'backup.sql');
    }

    public function delete($id)
    {
        $backup = Backup::find($id);
        @unlink('core/storage/app/public/' . $backup->filename);
        $backup->delete();

        Session::flash('success', 'Database sql file deleted successfully!');
        return back();
    }
}
