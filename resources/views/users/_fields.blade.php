{{ csrf_field() }}

<div class="form-group">
    <label for="first_name">Nombre:</label>
    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Pepe" value="{{ old('first_name', $user->first_name) }}">
</div>

<div class="form-group">
    <label for="last_name">Apellidos:</label>
    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Pérez" value="{{ old('last_name', $user->last_name) }}">
</div>

<div class="form-group">
    <label for="email">Correo electrónico:</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="pedro@example.com" value="{{ old('email', $user->email) }}">

</div>

<div class="form-group">
    <label for="password">Contraseña:</label>
    <input type="password" class="form-control" name="password" id="password" placeholder="Mayor a 6 caracteres">

</div>

<div class="form-group">
    <label for="bio">Bio:</label>
    <textarea class="form-control" name="bio" id="bio">{{ old('bio', $user->profile->bio) }}</textarea>
</div>

<div class="form-group">
    <label for="profession_id">Profesión:</label>
    <select name="profession_id" id="profession_id" class="form-control">
        <option value="">Seleccione una profesión</option>
        @foreach($professions as $profession)
            <option value="{{ $profession->id }}"{{ old('profession_id', $user->profile->profession_id) == $profession->id ? ' selected' : '' }}>
                {{ $profession->title }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="twitter">Twitter:</label>
    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="Url de tu usuario de twitter" value="{{ old('twitter', $user->profile->twitter) }}">
</div>

<h5>Habilidades:</h5>

@foreach($skills as $skill)

    <div class="form-check form-check-inline">
        <input name="skills[{{ $skill->id }}]" class="form-check-input" type="checkbox" id="skill_{{ $skill->id }}" value="{{ $skill->id }}" {{ ($errors->any() ? old("skills.{$skill->id}") : $user->skills->contains($skill)) ? ' checked' : '' }}>
        <label class="form-check-label" for="skill_{{ $skill->id }}">{{ $skill->name }}</label>
    </div>

@endforeach

<h5 class="mt-3">Rol:</h5>

@foreach(trans('users.roles') as $role => $name)

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="role" id="role_{{ $role }}" value="{{ $role }}" {{ old('role', $user->role) == $role  ? ' checked' : ''}}>
        <label class="form-check-label" for="role_{{ $role}}">{{ $name }}</label>
    </div>

@endforeach

<h5 class="mt-3">Estado</h5>

@foreach(trans('users.state') as $state => $label)

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="state"
               id="state_{{ $state }}" value="{{ $state }}"
         {{ old('state', $user->$state) === $state ? ' checked' : '' }}>
        <label for="state_{{ $state }}">{{ $label }}</label>
    </div>

@endforeach