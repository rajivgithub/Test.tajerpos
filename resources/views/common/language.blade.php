<form method="post" action="{{ route('set-language') }}">
@csrf
    <div class="form-group text-end">
        <select name="site_language" class="site-language" onchange="this.form.submit()">
            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
            <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>Arabic</option>
        </select>
    </div>
</form>
