package.path = package.path .. ";./lualib/?.lua"

require"Drawable"
require"ProgressBar"
require"Session"

function decline(value, titles)
    value = math.abs(value)
    local value1 = value % 10
    if value > 10 and value < 20 then return titles[3] end
    if value1 > 1 and value1 < 5 then return titles[2] end
    if value1 == 1 then return titles[1] end
    return titles[3]
end

function draw24dx()
    --[[
        $NewYear = gmmktime(0, 0, 0, 1, 1, 2012);
        $Diff = $NewYear - time(); // разница в секундах
        $RemDays = (int)floor($Diff / 86400); // целых дней в этой разнице
        $RemTime = gmdate('H:i:s', $Diff % 86400); // остатки дня в чч:мм:сс
        echo "До нового года осталось $RemDays дн. и $RemTime"
    -- ]]
    ny = gmmktime(22, 0, 0, 12, 30, 2013)
    diff = time() - ny
    remdays = math.floor(diff / 86400)
    remtime = gmdate("H", diff % 86400)
    radio_usage = {
        "Пользуюсь рацией уже "
    }
    table.insert(radio_usage, remdays)
    table.insert(radio_usage, decline(remdays, { "день", "дня", "дней" }))
    table.insert(radio_usage, remtime)
    table.insert(radio_usage, decline(remtime, { "час", "часа", "часов" }))
    annotation(4, 20, table.concat(radio_usage, " "))
end

function drawUaz()
end

function draw()
    --draw24dx()
    --do return end

    if Request.referer then
        if re_match("#24dx\\.ru#", Request.referer) then
            draw24dx()
        end
    elseif Query.testmod then
        if _G[Query.testmod] then
            _G[Query.testmod]()
        end
    else
        load_gif("gifka.gif")
        setstrokeantialias(true)
        if Session.get('count') == nil or Session.get('count') >= 100 then
            Session.set('count', 0)
        else
            Session.set('count', Session.get('count') + 16)
        end

        pb = ProgressBar:new(2, 2, 234, 10)
        pb.gauge.margin = 2
        pb.borderColor = "green"
        pb.progressColor = "green"
        pb:setProgress(Session.get('count'))

        create_layer(true)
        pb = ProgressBar:new(2, 13, 200, 6)
        pb.borderColor = "red"
        pb.progressColor = "red"
        pb:setProgress(-10)

        clone_layer(true)

        pb = ProgressBar:new(50, 15, 60, 16)
        pb.borderColor = "black"
        pb.progressColor = "orange"
        pb:setProgress(50)

        -- motionblurimage(2,2,45)

        --create_layer(true)
    end
end

function afterDrawLayer(frame)
end

function afterDraw()
end