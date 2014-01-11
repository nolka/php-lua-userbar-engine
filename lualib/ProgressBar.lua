ProgressBar = Drawable:new()

ProgressBar.borderColor = "red"
ProgressBar.progressColor = "white"
ProgressBar.value = 0
ProgressBar.stepSize = 1
ProgressBar.gauge = {
    margin = 2
}
ProgressBar.maxValue = 100
ProgressBar.borderOpacity = 1
ProgressBar.backgroundOpacity = 0.2
ProgressBar.foregroundOpacity = 1

function ProgressBar:draw()
    -- saving default color
    local defstrokeCol = getstrokecolor()
    local deffillCol = getfillcolor()
    local getfillOpacity = getfillopacity()
    setstrokecolor(self.borderColor)
    setfillcolor(self.borderColor)
    setfillopacity(self.backgroundOpacity)
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

    local gaugeWidth = self.location.x + self.gauge.margin --+ (self.value*self.stepSize)
    if self.value >0 then
        gaugeWidth = gaugeWidth + (self.value*self.stepSize) - (self.gauge.margin*2)
    end


    -- drawing gauge
    rectangle(self.location.x + self.gauge.margin,
        self.location.y + self.gauge.margin,
        gaugeWidth ,
        self.location.y + self.size.height - self.gauge.margin)
    setstrokecolor(defstrokeCol)
    setfillcolor(deffillCol)
    setstrokeopacity(defstrokealpha)

    --annotation(0, self.location.y+10, self.value)
end

-- Устанавливает значение прогресс бара в процентах
function ProgressBar:setProgress(progress)
    self.value = self.maxValue*progress/100 --(self.size.width/100)*progress
    if self.value > self.maxValue then
        self.value = self.maxValue
    end
    self.stepSize = (self.size.width)/100
    self:draw()
end

-- Устанавливает значение прогресс бара в пикселях (Осторожнее с этим!)
function ProgressBar:setValue(value)
    self.value = value
    self:draw()
end