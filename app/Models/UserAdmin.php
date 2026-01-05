    <?php

    namespace App\Models;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class UserAdmin extends Authenticatable
    {
        use Notifiable;

        protected $table = 'admin'; // GANTI
        protected $primaryKey = 'admin_id'; // GANTI kalau beda

        protected $fillable = [
            'username',
            'email',
            'password',
        ];

        protected $hidden = [
            'password',
        ];
    }
