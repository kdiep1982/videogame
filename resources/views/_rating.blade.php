<script>
    @if ($event) window.livewire.on('{{$event}}',params=>{ @endif

    @if ($event)
    var container = document.getElementById(params.slug)
    @else
        var container = document.getElementById('{{$slug}}')
    @endif
    var bar = new ProgressBar.Circle(container, {
        color: 'white',
        // This has to be the same size as the maximum width to
        // prevent clipping
        strokeWidth: 6,
        trailWidth: 3,
        easing: 'easeInOut',
        trailColor:'#4A5568',
        duration: 2500,
        text: {
            autoStyleContainer: false
        },
        from: { color: '#48BB78', width: 6 },
        to: { color: '#48BB78', width: 6 },
        // Set default step function for all animate calls
        step: function(state, circle) {
            circle.path.setAttribute('stroke', state.color);
            circle.path.setAttribute('stroke-width', state.width);

            var value = Math.round(circle.value() * 100);
            if (value === 0) {
            circle.setText('0%');
            } else {
            circle.setText(value+'%');
            }

        }
        });

        @if($event)
            bar.animate(params.rating);
        @else 
            bar.animate({{$rating}} / 100);
        @endif

        @if($event) }) @endif
</script>