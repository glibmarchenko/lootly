@section('scripts')
    <script>
        function onChangeCategoryCreate() {
            let selectBox = document.getElementById("selectCategory"),
                selectedValue = selectBox.options[selectBox.selectedIndex].value,
                categories = {
                    @foreach ($categories as $value)
                    '{{ $value->id }}': '{{ $value->slug }}',
                    @endforeach
                };

            if (selectedValue) {
                let url = '{{ route('spark.kiosk.resources.create', [], false) }}';

                if (categories[selectedValue] === 'case-studies') {
                    url = '{{ route('spark.kiosk.resources.case-studies.create', [], false) }}';
                }

                if (window.location.pathname !== url) {
                    window.location.href = url + '?category_id=' + selectedValue;
                }
            }
        }

        function onChangeCategoryEdit() {
            let selectBox = document.getElementById("selectCategory"),
                selectedValue = selectBox.options[selectBox.selectedIndex].value,
                categories = {
                    @foreach ($categories as $value)
                    '{{ $value->id }}': '{{ $value->slug }}',
                    @endforeach
                };

            if (selectedValue) {
                let url = '{{ route('spark.kiosk.resources.edit', ['id' => $resource->id ?? null], false) }}';

                if (categories[selectedValue] === 'case-studies') {
                    url = '{{ route('spark.kiosk.resources.case-studies.edit', ['id' => $resource->id ?? null], false) }}';
                }

                if (window.location.pathname !== url) {
                    window.location.href = url + '?category_id=' + selectedValue;
                }
            }
        }
    </script>
@endsection
