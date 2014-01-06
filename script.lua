package.path = package.path .. ";./lualib/?.lua"

require"Drawable"
require"ProgressBar"
require"Session"

function draw()

    --setstrokeantialias(true)

    local graphics = list_files()
    for i = 1, #graphics / 4 do
        -- draw_image(graphics[math.random(#graphics)], math.random(300), math.random(130))
    end

    xc, yc = 150, 150
    dc, dy = 200, 200
    points = {}
    for i = -1500, 1500 do
        x = xc + i * 5
        y = (math.sin(i) * 15) + math.cos(yc) + yc
        table.insert(points, { x = x, y = y })
    end


    pb = ProgressBar:new(54, 10, 200, 10)
    pb.borderColor = "green"
    pb.progressColor = "green"
    pb:setProgress(math.random(100))

    create_layer(true)
    setstrokecolor("red")
    setstrokeantialias(true)


    -- local lid = clone_layer(true)

    for i = 1, _width + 4, 4 do
        create_layer(nil, true, 10)
        line(i - 4, 0, i + 8 - 4, _height)
    end

    create_layer(true)
    pb = ProgressBar:new(54, 10, 200, 10)
    pb.borderColor = "red"
    pb.progressColor = "red"
    pb:setProgress(math.random(100))
end

