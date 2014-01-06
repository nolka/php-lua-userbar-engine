package.path = package.path .. ";./lualib/?.lua"

require"Drawable"
require"ProgressBar"
require"Session"

function draw()

    --setstrokeantialias(true)

    pb = ProgressBar:new(54, 10, 200, 10)
    pb.borderColor = "green"
    pb.progressColor = "green"
    pb:setProgress(math.random(100))

    create_layer(true, 150)
    setstrokecolor("red")
    setstrokeantialias(true)


    for i = 1, _width + 4, 4 do
        line(i - 4, 0, i + 8 - 4, _height)
    end

    create_layer(true, 150)
    pb = ProgressBar:new(54, 10, 200, 10)
    pb.borderColor = "red"
    pb.progressColor = "red"
    pb:setProgress(math.random(100))
end

