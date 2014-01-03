ProgressBar = Drawable:new()

ProgressBar.borderColor = "red"
ProgressBar.progressColor = "white"
ProgressBar.value = 0
ProgressBar.maxValue = 100

function ProgressBar:draw()
    -- saving default color
    local defstrokeCol = getstrokecolor()
    local deffillCol = getfillcolor()
    local getfillOpacity = getfillopacity()
    setstrokecolor(self.borderColor)
    setfillcolor(self.borderColor)
    setfillopacity(0.2)
    -- drawing border
    polygon({
        {
            x = self.location.x,
            y = self.location.y
        }, {
            x = self.location.x + self.size.width,
            y = self.location.y
        },
        {
            x = self.location.x + self.size.width,
            y = self.location.y + self.size.height
        },
        {
            x = self.location.x,
            y = self.location.y + self.size.height
        },
    })

    -- saving colors
    local defstrokealpha = defstrokeCol.a
    setstrokecolor(self.progressColor)
    setfillcolor(self.progressColor)

    -- drawing gauge
    rectangle(self.location.x + 2, self.location.y + 2, self.location.x + self.value - 2, self.location.y + self.size.height - 2)
    setstrokecolor(defstrokeCol)
    setfillcolor(deffillCol)
    setstrokeopacity(defstrokealpha)
end

-- Устанавливает значение прогресс бара в процентах
function ProgressBar:setProgress(progress)
    self.value = (self.size.width/100)*progress
    self:draw()
end

-- Устанавливает значение прогресс бара в пикселях (Осторожнее с этим!)
function ProgressBar:setValue(value)
    self.value = value
    self:draw()
end